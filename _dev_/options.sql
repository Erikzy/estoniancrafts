-- remove old settings
DELETE FROM `ktt_options` WHERE `option_name` IN 
(
	'_facebook_app_id',
	'_facebook_app_secret',
	'_product_max_length',
	'_product_max_width',
	'_product_max_height',
	'_product_short_description_limit',
	'_product_description_limit',
	'_external_instruction_page_path',
	'_csv_import_export_page_path',
	'_order_instruction_page_path'
);

-- insert new
INSERT INTO `ktt_options` (`option_name`, `option_value`) VALUES
('_facebook_app_id', '1086359828163406'),
('_facebook_app_secret', 'e2ea7bc6990e540b4bfe4a5cfda2f6c6'),
('_product_max_length', '2000'),
('_product_max_width', '1500'),
('_product_max_height', '1500'),
('_product_short_description_limit', '100'),
('_product_description_limit', '300'),
('_external_instruction_page_path', 'external-links-instructions'),
('_csv_import_export_page_path', 'csv-import-export'),
('_order_instruction_page_path', 'order-instructions');