-- Mark Passport OAuth migrations as executed if they don't exist in migrations table
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2025_11_18_100108_create_oauth_auth_codes_table', 1),
('2025_11_18_100109_create_oauth_access_tokens_table', 1),
('2025_11_18_100110_create_oauth_refresh_tokens_table', 1),
('2025_11_18_100111_create_oauth_clients_table', 1),
('2025_11_18_100112_create_oauth_device_codes_table', 1);
