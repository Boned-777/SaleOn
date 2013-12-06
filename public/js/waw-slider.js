	$(function () {
		
		var JsonDataMock = '{"final_json_data":[{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"777Киевская","brand_name":"Samsung","partner_name":"777Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"},{"post_id":"13","post_full_url":"/Proverkа-brendа/","oblast":"Черновицкая","brand_name":"Adidas","partner_name":"Сильпо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/range-rover-evoque-poluchil-shikarnyi-kostyum-ot-atele-prior-design-984x656-85716_1369902788.jpg&q=100&w=240&h=166","days_left":"17","days_left_text":"Осталось дней"},{"post_id":"14","post_full_url":"/Printer-HP/","oblast":"Черновицкая","brand_name":"HP","partner_name":"Все для дома","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/407459_1371589991.jpg&q=100&w=240&h=166","days_left":"41","days_left_text":"Осталось дней"},{"post_id":"10","post_full_url":"/esovyj-brend/","oblast":"Киевская","brand_name":"Samsung","partner_name":"Фокстрот","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/item2_1371595006.jpg&q=100&w=240&h=166","days_left":"1","days_left_text":"Осталось дней"},{"post_id":"11","post_full_url":"/Super-аkcziya/","oblast":"Одесская","brand_name":"ДП","partner_name":"Ельдорадо","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/02_1370337048.jpg&q=100&w=240&h=166","days_left":"15","days_left_text":"Осталось дней"},{"post_id":"12","post_full_url":"/Printer/","oblast":"Львовская","brand_name":"Nokia","partner_name":"Комфи","favorites_link":"/add_to_favorites.php?id=12345","photoimg":"/uploader_test/includes/timthumb.php?src=http://sherules.com.ua/uploader_test/uploads/357183_1371588007.jpg&q=100&w=240&h=166","days_left":"78","days_left_text":"Осталось дней"}]}';
		
		var ITEMS_PER_PAGE = 12,
			HIDDEN_PAGE_SHIFT = 2,
			NEXT_PREVIOUS_PAGE_SHIFT = 1,
			EMPTY_STRING = "";
		
		var wawSlyder = function(sliderData) {

		this.carouselContainer = $('#myCarousel');
			
			this.rowTemplate = ['<div class="row-fluid">', '</div>'];
			this.itemTemplate = '<div class="span3 bottom-offset">\
									<div class="img-wrapper">\
										<img  src="/ads/$imageLink" class="img-polaroid">\
										<div class="img-info">\
											<a href="$favoriteLink" class="favorites-icon"></a>\
											<a href="/ad/index/id/$link" class="post-link"><p class="ellipsis">$name</p></a>\
											<p class="ellipsis brand">$brand</p>\
											<p class="ellipsis">$daysMsgText: $daysLeft</p>\
										</div>\
									</div>\
								</div>';	
			this.init(sliderData);	 
		};
		
		wawSlyder.prototype = {
			
			init : function (sliderData) {
				//var jsonMockObj = JSON.parse(JsonDataMock);
				//var mockData = jsonMockObj && jsonMockObj.final_json_data || {};
				//this.data = mockData.concat(mockData);
				this.data = sliderData;

				this.currentPage = 1;
				this.bindEvents();
				this.initStartPages();
				this.initCarousel();
				this.displayCurrentPage();
			},

			bindEvents : function () {

				var itemWrapper 	= $(".items-wrapper"), 
					rightPagingBtn 	= $("#right-paging"),
					leftPagingBtn 	= $("#left-paging"),
					that			= this;

				itemWrapper.on("click", function(e){
					if ($(e.currentTarget).parent().hasClass("hover-right")){
						rightPagingBtn.trigger("click", {
							postSlideCallback : function() {that.buildNextHiddenPage()}
						});
					}
					if ($(e.currentTarget).parent().hasClass("hover-left")){
						leftPagingBtn.trigger("click", { 
							postSlideCallback : function() {that.buildPreviousHiddenPage()}
						});
					}
					$(e.target).parent().hasClass("post-link") || e.preventDefault();
				})
			},
					
			initStartPages : function () {
				
				this.isCycleAvailable() && this.buildPage($(".hover-left.hide-left.item").find(".items-wrapper"), this.getIndexes(this.getPageCount()-1));			
				this.isCycleAvailable() && this.buildPage($(".hover-left.visible.item").find(".items-wrapper"), this.getIndexes(this.getPageCount()));
											
				this.buildPage($(".active").find(".items-wrapper"), this.getIndexes(1));
				this.buildPage($(".hover-right.visible.item").find(".items-wrapper"), this.getIndexes(2));
				
				this.isCycleAvailable() && this.buildPage($(".hover-right.hide-right.item").find(".items-wrapper"), this.getIndexes(3));
			},

			initCarousel : function () {
			    this.carouselContainer.carousel({
					interval: false
				});
			},
			
			buildPage : function (container, indexes){
				var	j		= 0,
					data   	= this.data,
					result 	= EMPTY_STRING;
					
				for (var i = indexes.startIndex; i <= indexes.endIndex; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (data[i]) {
							result += this.itemTemplate
								.replace("$imageLink", (data[i].photoimg))
								.replace("$link", data[i].post_id)
								.replace("$favoriteLink", data[i].favorites_link)
								.replace("$name", data[i].partner_name)
								.replace("$brand", data[i].brand_name)
								.replace("$daysLeft", data[i].days)
								.replace("$daysMsgText", data[i].days_left_text); 
						}
					if (j==3) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				container.html(result);
				
				//console.log("Page is :" + indexes.startIndex +"="+indexes.endIndex);
			},
			
			buildNextHiddenPage : function (){
				this.currentPage = this.getNextPage(this.currentPage + NEXT_PREVIOUS_PAGE_SHIFT);
				this.displayCurrentPage();
				if (this.isCycleAvailable()) {
					var pageToBuild  = this.getNextPage(this.currentPage + HIDDEN_PAGE_SHIFT);
					var container = $(".hover-right.hide-right.item").find(".items-wrapper");
					this.buildPage(container, this.getIndexes(pageToBuild));
				}
			},
			
			buildPreviousHiddenPage : function (){
				this.currentPage = this.getNextPage(this.currentPage - NEXT_PREVIOUS_PAGE_SHIFT);
				this.displayCurrentPage();
				if (this.isCycleAvailable()) {
					var pageToBuild  = this.getNextPage(this.currentPage - HIDDEN_PAGE_SHIFT);
					var container = $(".hover-left.hide-left.item").find(".items-wrapper");
					this.buildPage(container, this.getIndexes(pageToBuild));
				}
			},
			
			getIndexes : function (page) {
				return {
					startIndex 	: (page - 1) * ITEMS_PER_PAGE,
					endIndex 	: (page * ITEMS_PER_PAGE) - 1
				}
			},
			
			getPageCount : function () {
				return Math.ceil(this.data.length / ITEMS_PER_PAGE);
			},
			
			getNextPage : function (page) {
				var pageCount = this.getPageCount();
				if (page == (pageCount + 1)) 	{return 1}
				if (page == (pageCount + 2)) 	{return 2}
				
				if (page == 0) 			{return pageCount}
				if (page == -1)			{return pageCount - 1}
				return page;
			},
			
			isCycleAvailable : function (page) {
				var pageCount = this.getPageCount();
				return (pageCount >= 3);
			},

			displayCurrentPage : function () {
				var pageNumber = $("#page-number");	
				pageNumber.html(this.currentPage);
				pageNumber.parent().show();
			}
			
				
		};
		
		/**
		run waw slider
		*/
		$(".lock-loading").show();
		$.ajax({
			url: "/ad/list",
	        dataType: "json",
			cache: false
		}).done(function( data ) {
			var slider = new wawSlyder(data.concat(data).concat(data).concat(data).concat(data).concat(data).concat(data).concat(data).concat(data).concat(data).concat(data));
			//var slider = new wawSlyder(data);
			$(".lock-loading").hide();

		}).fail(function( data ) {
			$(".no-data").show();
			$("#myCarousel").hide();
			$(".lock-loading").hide();

		});
	})
