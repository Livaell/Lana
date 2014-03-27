SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_slider]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_slider] ADD [caption_position] [nvarchar](150) DEFAULT 'bottom'
END;