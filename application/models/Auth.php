<?php

class Application_Model_Auth
{
    public function process($username, $password)
    {
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            $auth->getStorage()->write($user);
            $userObj = new Application_Model_User();
            $userObj->setGlobalLocale($user->locale);

            return $user;
        }
        return false;
    }

    protected function _getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt)) AND deleted=0');

        return $authAdapter;
    }
}