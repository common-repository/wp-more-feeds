<?php
/*
Plugin Name: WP More Feeds
Plugin URI: http://www.mashget.com/2008/09/10/wp-more-feeds-for-wordpress/
Description: Generate RSS feeds for category and tag pages
Author: Andrew Zhang
Version: 0.17
Author URI: http://www.mashget.com
*/

class MoreFeeds
{
	var $plugin_name="WP More Feeds";
	var $plugin_version="0.17";
	var $plugin_uri="http://www.mashget.com";
	var $currentRssInfo=null;
	var $mf_options=null;
	var $feedbase;
	
	function MoreFeeds()
	{
		add_action('wp_head', array(&$this,'generate_wpfeeds'));
		add_action('admin_menu', array(&$this,'wp_add_options_page'));
		
		$this->mf_options = get_option("wp_more_feeds");	
		if(!$this->mf_options)
		{
			$this->mf_options=$this->wp_load_default_options();
		}
	}
	
	function initFeedBase()
	{
		$this->feedbase=get_bloginfo('rss2_url', 'display');
		if(strpos($this->feedbase,"?")===false)$this->feedbase.="?";
		else $this->feedbase.="&";
	}
	
	function wp_add_options_page() 
	{
		if (function_exists('add_options_page'))
		{
			add_options_page( $this->plugin_name, $this->plugin_name, 8, basename(__FILE__), array(&$this,'wp_more_feeds_options_subpanel'));
		}
	}
	
	function wp_load_default_options()
	{
		$wp_mf_ops= array (
				"cat_page" 	=> "1",
				"cat_title" => "%category_name% | ".get_bloginfo('name','display')." RSS Feed",
				"tag_page"	=> "1",
				"tag_title" => "%tag_name% | ".get_bloginfo('name','display')." RSS Feed",
		);
		update_option("wp_more_feeds",$wp_mf_ops);
		return $wp_mf_ops;
	}
	
	function wp_more_feeds_options_subpanel()
	{
		if($_POST["wp_mf_submit"])
		{
			$wp_mf_ops = array (
				"cat_page" 	=> $_POST['mfeeds_cat_page'],
				"cat_title" => $_POST['mfeeds_cat_title'],
				"tag_page"	=> $_POST['mfeeds_tag_page'],
				"tag_title" => $_POST['mfeeds_tag_title'],
				"post_page_cat" => $_POST['mfeeds_post_page_cat'],
				"post_page_tag" => $_POST['mfeeds_post_page_tag'],
				"tag_pmincount" => $_POST['mfeeds_tag_pmincount'],
			);
			update_option("wp_more_feeds",$wp_mf_ops);
			echo '<div id="message" class="updated fade"><p>Options Updated</p></div>';
		}
		else if($_POST["wp_mf_load_default"])
		{
			$wp_mf_ops=$this->wp_load_default_options();
			echo '<div id="message" class="updated fade"><p>Options Reset</p></div>';
		}
		else 
		{
			$wp_mf_ops=$this->mf_options;
		}
		?>

<div class="wrap">
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=more-feeds.php" method="post">
    <h2><?php echo $this->plugin_name;?> Options</h2>
    <h3>Categories</h3>
    <table class="form-table">
      <tr valign="top">
        <td><input name="mfeeds_cat_page" type="checkbox" id="mfeeds_cat_page" value="1" <?php checked('1', $wp_mf_ops['cat_page']); ?> />
          <label for=mfeeds_cat_page><strong>Generate feed for categories</strong> </label>
        </td>
      </tr>
      <tr valign="top">
        <td><strong>Category Feed Title Format:</strong><br/>
          <input name="mfeeds_cat_title" type="text" size=50 id="mfeeds_cat_title" value="<?php echo $wp_mf_ops['cat_title']; ?>" />
        </td>
      </tr>
    </table>
    <h3>Tag Archives</h3>
    <table class="form-table">
      <tr valign="top">
        <td><input name="mfeeds_tag_page" type="checkbox" id="mfeeds_tag_page" value="1" <?php checked('1', $wp_mf_ops['tag_page']); ?> />
          <label for=mfeeds_tag_page><strong>Generate feed for Tag Archives</strong></label> (Just for the tags having at least <input name="mfeeds_tag_pmincount" type="text" size=3 id="mfeeds_tag_pmincount" value="<?php echo $wp_mf_ops['tag_pmincount']; ?>" /> posts)
        </td>
      </tr>
      <tr valign="top">
        <td><strong>Tag Feed Title Format:</strong><br/>
          <input name="mfeeds_tag_title" type="text" size=50 id="mfeeds_tag_title" value="<?php echo $wp_mf_ops['tag_title']; ?>" />
        </td>
      </tr>
    </table>
    <h3>Posts</h3>
    <table class="form-table">
      <tr valign="top">
        <td><input name="mfeeds_post_page_cat" type="checkbox" id="mfeeds_post_page_cat" value="1" <?php checked('1', $wp_mf_ops['post_page_cat']); ?> />
          <label for=mfeeds_post_page_cat><strong>Generate category feeds for post</strong></label>
        </td>
      </tr>
      <tr valign="top">
        <td><input name="mfeeds_post_page_tag" type="checkbox" id="mfeeds_post_page_tag" value="1" <?php checked('1', $wp_mf_ops['post_page_tag']); ?> />
          <label for=mfeeds_post_page_tag><strong>Generate tag feeds for post</strong></label>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="wp_mf_load_default" value="Reset to Default Options &raquo;" class="button" onclick="return confirm('Are you sure to reset options?')" />
      <input type="submit" name="wp_mf_submit" value="Save Options &raquo;" class="button" style="margin-left:15px;" />
    </p>
  </form>
</div>
<?php
	}
	
	function getCatRss($catID)
	{
		if (!empty($catID) && !(strtoupper($catID) == 'ALL'))
		{
			$catID = intval($catID);
			$category = &get_category($catID);
			if (is_wp_error( $category ))
				return false;
			if($category->slug)
			{
				return array(
					'ID'=>$catID,
					'name'=>$category->name,
					'slug'=>$category->slug,
					'queryvars'=>'category_name='.urlencode($category->slug),
					"type"=>"category"
				);
			}
		}
		return false;
	}
	
	function getTagRss($tagID)
	{
		if (!empty($tagID))
		{
			$tagID=intval($tagID);
			$my_tag = &get_term($tagID, 'post_tag', OBJECT, 'display');
			if ( is_wp_error( $my_tag ) )
				return false;
			if($my_tag->slug)
			{
				if($this->mf_options['tag_pmincount'])
				{
					$pmin=intval($this->mf_options['tag_pmincount']);
					if($my_tag->count < $pmin)return false;
				}
				return array(
					'ID'=>$tagID,
					'name'=>$my_tag->name,
					'slug'=>$my_tag->slug,
					'queryvars'=>'tag='.urlencode($my_tag->slug),
					"type"=>"tag"
				);
			}
		}	
		return false;
	}
	
	function output_wpfeeds($rssinfo)
	{
		if($rssinfo)
		{	
			switch ($rssinfo['type'])
			{
				case 'category':
					$feedlink=get_category_feed_link($rssinfo['ID']);
					break;
					
				case 'tag':
					$feedlink=get_tag_feed_link($rssinfo['ID']);
					break;
					
				default:
					$feedlink=$this->feedbase.$rssinfo['queryvars'];
					break;
			}
			
			$feedtitle=$rssinfo['title'];		
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"$feedtitle\" href=\"$feedlink\" />\n";
				
			$this->currentRssInfo=array('title'=>$feedtitle, 'link'=>$feedlink);
		}
		else 
		{
			$this->currentRssInfo=false;
		}	
	}
	
	function generate_wpfeeds()
	{
		echo "<!--$this->plugin_name $this->plugin_version ($this->plugin_uri) Begin -->\n";
		$this->initFeedBase();
		
		if(is_category()&&$this->mf_options['cat_page'])
		{
			$rssinfo = MoreFeeds::getCatRss(get_query_var('cat'));	
			if($rssinfo)
			{
				$rssinfo['title']=str_replace('%category_name%', $rssinfo['name'], $this->mf_options['cat_title']);
				$this->output_wpfeeds($rssinfo);
			}
		}
		else if(is_tag()&&$this->mf_options['tag_page'])
		{
			$rssinfo = MoreFeeds::getTagRss(get_query_var('tag_id'));
			if($rssinfo)
			{
				$rssinfo['title']=str_replace('%tag_name%', $rssinfo['name'], $this->mf_options['tag_title']);
				$this->output_wpfeeds($rssinfo);
			}
		}	
		else if(is_single())
		{
			global $post;
			if($this->mf_options['post_page_cat'])
			{
				$categories = get_the_category($post->ID);
				foreach ($categories as $category)
				{
					$rssinfo = MoreFeeds::getCatRss($category->cat_ID);
					if($rssinfo)
					{
						$rssinfo['title']=str_replace('%category_name%', $rssinfo['name'], $this->mf_options['cat_title']);
						$this->output_wpfeeds($rssinfo);
					}
				}
			}
			if($this->mf_options['post_page_tag'])
			{
				if (function_exists('get_the_tags')) 
				{
					$tags = get_the_tags($post->ID);
	            	if ($tags && is_array($tags)) 
	            	{
	                	foreach ($tags as $tag) 
	                	{
	                		$rssinfo = MoreFeeds::getTagRss($tag->term_id);
	                		if($rssinfo)
							{
								$rssinfo['title']=str_replace('%tag_name%', $rssinfo['name'], $this->mf_options['tag_title']);
								$this->output_wpfeeds($rssinfo);
							}
	                	}
	            	}
				}
			}
		}
		
		echo "<!--$this->plugin_name End -->\n";
	}
}

$morefeeds=&new MoreFeeds();
?>
