SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_slider]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_slider] 
ADD 
	[click_action] [nvarchar](150) DEFAULT 'no_action',
  	[open_link_in] [nvarchar](150) DEFAULT 'current_browser'
END;