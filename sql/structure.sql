CREATE TABLE `Inscrits` (
	`id` int(11) NOT NULL,
	`nom` varchar(255) NOT NULL,
	`prenom` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`metier` varchar(255) NOT NULL,
	`message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `Inscrits`
	ADD PRIMARY KEY (`id`);
ALTER TABLE `Inscrits`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
