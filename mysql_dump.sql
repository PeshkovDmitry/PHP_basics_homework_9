CREATE TABLE application1.users (
	id_user INT PRIMARY KEY AUTO_INCREMENT, 
	user_login VARCHAR(45),
	user_name VARCHAR(45), 
	user_lastname VARCHAR(45), 
	user_birthday_timestamp INT,
	user_password_hash VARCHAR(255),
	user_role VARCHAR(255)) 
	DEFAULT CHARACTER SET = utf8;
	-- пароль администратора - admin
INSERT INTO application1.users(
		user_login, 
		user_name, 
		user_lastname, 
		user_birthday_timestamp, 
		user_password_hash, user_role) 
	VALUES (
		'admin', 
		'admin', 
		'admin', 
		0, 
		'$2y$10$tHoz1WT3jTkeuWhOnK9yYeDP02Hs2evfwF1gpgQ/zzRHwu10iDMz2', 
		'admin');