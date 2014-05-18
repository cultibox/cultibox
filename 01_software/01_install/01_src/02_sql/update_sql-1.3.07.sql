SET CHARACTER SET utf8;

-- Version 1.3.07:
DROP TABLE `cultibox`.`historic`;
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `SHOW_HISTORIC`;

DELETE FROM `cultibox`.`notes`;
INSERT INTO `cultibox`.`notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES
(1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'),
(2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'),
(3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'http://www.cultibox.fr', 'external', 'fr_FR'),
(4, 'Recyclage', 'Chez Cultibox nous retraitons tous les éléments de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'),
(5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'),
(6, 'Documentation', 'Find a more complete documentation in the software by clicking on the <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Help tab</a>. The most current version is available using the following address:', NULL, 'https://code.google.com/p/cultibox', 'external', 'en_GB'),
(7, 'Some questions?', 'If help is not enough to answer one of your question, send us an email at the following address:', NULL, 'support@cultibox.fr', 'mail', 'en_GB'),
(8, 'Accessories', 'You can complete your package by purchasing additional sensors, 1000W and 3500W plugs or other accessories by visiting the website:', NULL, 'http://www.cultibox.fr', 'external', 'en_GB'),
(9, 'Recycling', 'At Cultibox we reprocess all part of our products. The packaging can be recycled and Cultibox contains a lithium battery that should not be thrown away. For optimal recycling, return us the Cultibox and you will be rewarded.', 'recycling.png', NULL, NULL, 'en_GB'),
(10, 'Warrantly', 'The Cultibox and accessories are warranted for two years. We ensure the security directly, without intermediary. To contact us:', NULL, 'support@cultibox.fr', 'mail', 'en_GB'),
(11, 'Documentación', 'Encuentra una documentación más completa en el software haciendo clic en la pestaña  <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Ayuda</a>. La versión más actualizada se encuentra disponible la siguiente dirección:', NULL, 'https://code.google.com/p/cultibox', 'external', 'es_ES'),
(12, '¿Preguntas?', 'Si la ayuda no es suficiente para cumplir con una de sus preguntas, envíe un correo electrónico a la siguiente dirección:', NULL, 'support@cultibox.fr', 'mail', 'es_ES'),
(13, 'Accesorios', 'Puede completar su paquete mediante la compra de sensores adicionales, 1000W y 3500W accesorios tomadas u otro, visitando el sitio web:', NULL, 'http://www.cultibox.fr', 'external', 'es_ES'),
(14, 'Reciclaje', 'En Cultibox que reprocesar parte de nuestros productos. El embalaje es reciclable y Cultibox contiene una batería de litio que no debe ser desechada. Para un reciclaje óptimo, nos devuelven Cultibox y serás recompensado.', 'recycling.png', NULL, NULL, 'es_ES'),
(15, 'Garantía', 'El Cultibox y accesorios tienen una garantía de dos años. Garantizamos la seguridad directamente, sin intermediario. Para contactar con nosotros:', NULL, 'support@cultibox.fr', 'mail', 'es_ES'),
(16, 'Documentazione', 'Trova una documentazione più completa del software facendo clic sulla scheda <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Guida</a>. La versione più aggiornata è disponibile al seguente indirizzo:', NULL, 'https://code.google.com/p/cultibox', 'external', 'it_IT'),
(17, 'Domande?', 'Se l''aiuto non è sufficiente a soddisfare una delle vostre domande, inviateci una e-mail al seguente indirizzo:', NULL, 'support@cultibox.fr', 'mail', 'it_IT'),
(18, 'Accessori', 'È possibile completare il pacchetto con l''acquisto di sensori supplementari, 1000W e 3500W accessori adottate o altro visitando il sito:', NULL, 'http://www.cultibox.fr', 'external', 'it_IT'),
(19, 'Riciclaggio', 'In Cultibox abbiamo rielaborare tutti parte dei nostri prodotti. L''imballaggio può essere riciclato e Cultibox contiene una batteria al litio che non deve essere gettato via. Per il riciclaggio ottimale, noi tornare Cultibox e sarete ricompensati.', 'recycling.png', NULL, NULL, 'it_IT'),
(20, 'Garanzia', 'Il Cultibox e gli accessori sono garantiti per due anni. Noi garantiamo la sicurezza direttamente, senza intermediari. Per contattarci:', NULL, 'support@cultibox.fr', 'mail', 'it_IT'),
(21, 'Dokumentation', 'Finden Sie eine umfassendere Dokumentation in der Software, indem Sie auf der <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank"> Registerkarte Hilfe</a>. Die aktuelle Version ist verfügbar unter folgender Adresse:', NULL, 'https://code.google.com/p/cultibox', 'external', 'de_DE'),
(22, 'Haben Sie Fragen?', 'Ist die Beihilfe ist nicht genug, um eine Ihrer Fragen gerecht zu werden, senden Sie uns eine E-Mail an die folgende Adresse:', NULL, 'support@cultibox.fr', 'mail', 'de_DE'),
(23, 'Zubehör', 'Sie können Ihr Paket durch den Kauf von zusätzlichen Sensoren, 1000W und 3500W genommen oder anderem Zubehör durch den Besuch der Website zu vervollständigen:', NULL, 'http://www.cultibox.fr', 'external', 'de_DE'),
(24, 'Recycling', 'In Cultibox wir nachbearbeiten alle Teil unserer Produkte. Die Verpackung kann recycelt werden und Cultibox enthält eine Lithium-Batterie, die nicht geworfen sollten entfernt werden. Für eine optimale Wiederverwertung, bringen uns Cultibox und Sie werden belohnt werden.', 'recycling.png', NULL, NULL, 'de_DE'),
(25, 'Garantie', 'Die Cultibox und Zubehör sind für zwei Jahre garantiert. Wir sorgen für die Sicherheit direkt, ohne Vermittler. Um uns zu kontaktieren:', NULL, 'support@cultibox.fr', 'mail', 'de_DE');

