ALTER TABLE `#__imageshow_theme_slider`
ADD COLUMN `click_action` char(150) DEFAULT 'no_action',
ADD COLUMN `open_link_in` char(150) DEFAULT 'current_browser';