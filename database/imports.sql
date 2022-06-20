TRUNCATE TABLE `servers`.`package`;

INSERT INTO
    `servers`.`package` (`id`, `name`, `ram_size`, `disk_size`, `processor_power`, `cost`, `image_src`)
VALUES
      (1, 'Standard', 1024, 2048, 100, 25, 'views/assets/mc_server_1.jpg'),
      (2, 'Premium', 2048, 8192, 200, 50, 'views/assets/mc_server_2.png');
