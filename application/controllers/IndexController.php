<?php
require_once APPLICATION_PATH.'/../library/Google/Google_Client.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_PlusService.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_Oauth2Service.php';

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $this->user = $auth->getIdentity();
        }
    }

    public function indexAction()
    {
        $this->view->subscriptionForm = new Application_Form_Subscription();
        $params = $this->getAllParams();

        if (isset($params["sort"]) && $params["sort"] === "favorite") {
            if (
                $this->user === null ||
                $this->user->role !== Application_Model_User::USER
            ) {
                $this->_helper->redirector("index");
            }
        }

        $preparedParams = $this->prepareParams($params);
        $pageTitle = implode(" / ", $preparedParams["filterNames"]);
        $this->view->headTitle($pageTitle);
        $this->view->headTitle()->setSeparator(' / ');
        $userId = isset($this->user) ? $this->user : null;

        if ($userId) {
            $preparedParams["filterParams"]["user_id"] = $userId;
            $preparedParams["filterParams"]["favorites_list"] = $this->user->favorites_ads ? $this->user->favorites_ads : "";
        }

        $ad = new Application_Model_Ad();
        $res = $ad->getList($preparedParams["filterParams"], true);
        $this->view->items = $res;
    }

    protected function prepareParams($data) {
        $filtersList = array("geo", "category", "brand", "product");
        $filters = array();
        $names = array();
        foreach($filtersList as $filterName) {
            if (isset($data[$filterName]) && $data[$filterName] != "all") {
                $filterClass = "Application_Model_" . ucfirst($filterName);
                $filter = new $filterClass();
                $filterValue = $filter->getByAlias($data[$filterName]);
                if ($filterValue) {
                    $filters[$filterName] = $filterValue;
                    $names[$filterName] = $filter->getName();
                }
            }
        }
        if (!empty($data["sort"])) {
            $filters["sort"] = $data["sort"];
        }
        return array(
            "filterParams" => $filters,
            "filterNames" => $names
        );
    }

    public function contactsAction()
    {
        global $translate;
        {

            $layout = Zend_Layout::getMvcInstance();
            $view = $layout->getView();
            $form = new Application_Form_Contact();

            $request = $this->getRequest();

            if ($request->isPost()) {
                $data = $request->getPost();

                if ($form->isValid($data)) {
                    $message = "<h2>" . $data['name'] . "</h2><p>Email:&nbsp;" . $data['email'] . "</p><p>" . $data['message'] . "</p>";

                    $email = new Application_Model_MandrillAdapter();
                    $sendRes = $email->sendHTML('Новое сообщение через форму связи', $message,
                        array(
                            "saleoninfo@gmail.com" => "SaleON Admin"
                        ),
                        array(
                            'headers' => array(
                                'Reply-To' => $data['email']
                            ),
                            'important' => true
                        )
                    );
                    if ($sendRes !== false) {
                        $view->successMessage = $translate->getAdapter()->translate("contact_ok");
                    }
                    return true;
                }
                $view->errorMessage = $translate->getAdapter()->translate("error");;
            }
            $this->view->form = $form;
        }
    }
}

