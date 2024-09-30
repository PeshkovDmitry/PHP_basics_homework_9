CREATE TABLE application1.users (
	id_user INT PRIMARY KEY AUTO_INCREMENT, 
	user_name VARCHAR(45), 
	user_lastname VARCHAR(45), 
	user_birthday_timestamp INT) 
	DEFAULT CHARACTER SET = utf8;