SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_slider]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_slider](
  [theme_id] [int] IDENTITY(1,1) NOT NULL,  
  [img_transition_effect] [nvarchar](30) DEFAULT 'random',
  [toolbar_navigation_arrows_presentation] [nvarchar](30) DEFAULT 'show-on-mouse-over',
  [toolbar_slideshow_player_presentation] [nvarchar](30) DEFAULT 'hide',
  [caption_show_caption] [nvarchar](30) DEFAULT 'yes', 
  [caption_title_css] [nvarchar](255) DEFAULT '',
  [caption_description_css] [nvarchar](255) DEFAULT '',
  [caption_link_css] [nvarchar](255) DEFAULT '',
  [caption_caption_opacity] [nvarchar](30) DEFAULT '75',
  [caption_title_show] [nvarchar](30) DEFAULT 'yes',
  [caption_description_show] [nvarchar](30) DEFAULT 'yes',
  [caption_link_show] [nvarchar](30) DEFAULT 'no',
  [caption_position] [nvarchar](150) DEFAULT 'bottom',   
  [slideshow_slide_timming] [nvarchar](30) DEFAULT '6',
  [slideshow_pause_on_mouseover] [nvarchar](30) DEFAULT 'yes',
  [slideshow_auto_play] [nvarchar](30) DEFAULT 'yes',
  [thumnail_panel_position] [nvarchar](30) DEFAULT 'right',
  [thumbnail_panel_presentation] [nvarchar](30) DEFAULT 'show',
  [thumbnail_presentation_mode] [nvarchar](30) DEFAULT 'numbers',
  [thumbnail_active_state_color] [nvarchar](30) DEFAULT '#CC3333',
  [click_action] [nvarchar](150) DEFAULT 'no_action',
  [open_link_in] [nvarchar](150) DEFAULT 'current_browser',
 CONSTRAINT [PK_#__imageshow_theme_slider_theme_id] PRIMARY KEY CLUSTERED 
(
	[theme_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;