<?php

/**
 * Template Class
 *
 * Handles the templating, functionality atm:
 *
 * - Foreach Loops
 * - Standard Tags Replacement
 * - Conditional Statements - Boolean
 *
 * All based on [ ] tags
 * 
 * @author Ashley Banks
 * Version: 1.2.1
*/

class Templater {

	private $_main_content,
			$_template_tags;
			
	private static $_instance = NULL;
	
	public static function getInstance () 
	{
		if ( !self::$_instance instanceof self ) 
			self::$_instance = new self();
		
		 return self::$_instance;
	}	
	
	private function __Construct () {}

	/**
	 * Set up the content and return the rendered template.
	 *
	 * @param string $template - the 'view' to render
	 * @param assoc array $tags - the tags to replace
	 */
	public function set_content ( $template, $tags, $use_header = TRUE, $use_footer = TRUE )
	{
		if ( $use_header )
			$this->_main_content .= file_get_contents ( $_SERVER['DOCUMENT_ROOT'] . '/' . DIRECTORY . '/Views/overall_header.html' ); flush();
		
		$this->_main_content .= file_get_contents ( $_SERVER['DOCUMENT_ROOT'] . '/' . DIRECTORY . '/Views/' . $template . '.html' ); flush();
		
		if ( $use_footer )
			$this->_main_content .= file_get_contents ( $_SERVER['DOCUMENT_ROOT'] . '/' . DIRECTORY . '/Views/overall_footer.html' ); flush();
		
		
		$this->_template_tags = $tags;

		$this->find_tags ();
		
		$this->render ();
	}


	/**
	 * Find tags and render them.
	 */
	private function find_tags ()
	{
		// Set the initial tags match
		preg_match_all( "|\[(.*)\]|U", $this->_main_content, $matches );	

		// Loops through the found tags

		for( $i = 0; $i < count( $matches[ 1 ] ); $i++ )
		{
			// If a tag is a FOREACH we loop through the results by the given tag
			
			if ( substr ( $matches[ 1 ][ $i ], 0, 7 ) == 'FOREACH' )
			{
				// get the Array to loop
				// get the tag to replace content with

				$match = explode ( ' ', $matches[ 1 ][ $i ] );

				// Use foreach function

				$this->_handle_foreach( $match[1], $match[3] );	
			}
			
			elseif ( substr ( $matches[ 1 ][ $i ], 0, 2 ) == 'IF' )
			{
				$match = explode ( ' ', $matches[ 1 ][ $i ] );

				$this->_conditional( $match[1] );	
			}
			else
			{
				// If its not a foreach tag or other stuff we can set the ordinary tags.
				// Stops it from trying to replace and bloat shit that aint there.

				if ( substr ( $matches[ 1 ][ $i ], 0, 7 ) !== 'FOREACH' &&  substr ( $matches[ 1 ][ $i ], 0, 2 ) !== 'IF' && substr ( $matches[ 1 ][ $i ], 0, 2 ) !== '.' && substr ( $matches[ 1 ][ $i ], 0, 1 ) !== '/'  )
				{
					$this->set_tags ( $matches[1] );
				}
			}
		}
	}

	/**
	 * Swaps the tags for ordinary tags
	 * @param array $tags - the tags to replace.
	 */

	private function set_tags ( $tags )
	{
		if ( is_array( $tags ) )
		{	
			foreach ( $tags as $tag )
			{
				$replace = $this->_template_tags[ strtolower ( $tag ) ];
				
				if ( !!$replace  )
				{	
					if ( $replace == ' ' )
						$this->_main_content = str_replace ( '[' . $tag . ']', '', $this->_main_content );
					elseif ( $replace == '' )
						$this->_main_content = str_replace ( '[' . $tag . ']', '', $this->_main_content );
					elseif ( preg_match("#\\[(.+)\\]#s", $replace ) === 1 )
						$this->_main_content = str_replace ( '[' . $tag . ']', '', $this->_main_content );
					else
						$this->_main_content = str_replace ( '[' . $tag . ']', $replace, $this->_main_content );
				}
				else
					continue;

			}
			return $this;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Handles conditonal tags within the view.
	 * This relies on TRUE / FALSE statements, so the conditions must be set in the PHP
	 * @param string $c - the key in the tag that has been sent
	 */
	private function _conditional ( $c )
	{
		if ( !!$c )
		{
			$condition = $this->_template_tags[ strtolower ( $c ) ];
			
			preg_match ( '#\\[IF ' . $c . '](.+)\\[/ENDIF ' . $c . ']#s', $this->_main_content, $matches );
			
			if ( $condition )
				$this->_main_content = preg_replace ( '#\\[IF ' . $c . '](.+)\\[/ENDIF ' . $c . ']#s', $matches[1], $this->_main_content );
			else
				$this->_main_content = preg_replace ( '#\\[IF ' . $c . '](.+)\\[/ENDIF ' . $c . ']#s', '', $this->_main_content );
			
			return $this;
		}
		else
			return FALSE;	
	}

	/**
	 * Handles the foreach loop for the tags
	 * 
	 * @param string $array - the array tag to loop through
	 * @param string $array_tag - the array key to replace with the tag ( ie. title )
	 *
	 * This is a DAMN messy function, needs alot of refactoring - but the main logic to get the thing
	 * working is set and working, which is the main thing.
	 */

	private function _handle_foreach ( $array, $array_tag )
	{
		// Get the array by the provided tags array
		
		$tag_array = $this->_template_tags[ $array ];
		
		// get everything between the foreach tags ( basically the tags to replace )
		
		preg_match ( '#\\[FOREACH ' . $array . ' as ' . $array_tag . '](.+)\\[/' . $array . ' FOREACH\\]#s', $this->_main_content, $matches2 );	
		
		$mtch = trim ( $matches2[1] );
		//$mtch = strip_tags ( $mtch );
		$mtch = preg_replace( '/\s+/', ' ', $mtch );
		$mtch = str_replace( '"', ' ', $mtch );
		$mtch = explode ( ' ', $mtch );
		
		
		// The content between the Foreach tags to loop through and build a string to output.

		$_for_content = implode ( ',', $matches2 );
		$_for_content = str_replace ( '[FOREACH ' . $array . ' as ' . $array_tag . ']', '', $_for_content );
		$_for_content = str_replace ( '[/' . $array . ' FOREACH]', '', $_for_content );
		//$_for_content = str_replace ( ' ', '', $_for_content);
		$_for_content = trim ( $_for_content );
		$_for_content = explode ( ',', $_for_content );
		
		
		// Set loop content - this is the narrowed down tags from above.

		$_loop_content = array();

		// Push into array so we don't have aload of junk
	
		foreach ( $_for_content as $_c )
		{
			if ( !in_array ( $_c, $_loop_content ) )
				array_push ( $_loop_content, $_c );	
		}

		// As its pushed in twice because of the regex we will take the second without the spacing.

		$_loop_content = $_loop_content[1];
		
		// Loop the tag array for the foreach loop function
	
		foreach ( $tag_array as $row )
		{
			// Set default values - this is so they strings and array start again
			// And don't become one long string - this is because we are building up
			// the loop content and replacing the specific tags

			$for_values = '';
			$new_for_tags = array ();
			$for_tags = '';
			
			// Loop through the items we want to replace and build up an array to 
			// use for the string replace
			
			for ( $i = 0; $i < count( $mtch ); $i++ )
			{
				$item = $mtch[$i];
				
				// Check that what we are grabbing are actual tags in  [ ]
				preg_match ( '#\\[' . $array_tag . '.(.+)\\]#s', $mtch[$i], $mtchhhh );
			
				$item = $mtchhhh[1];
				
				// If there is a match we go fourth, not to clog the array up with shit.
				if ( !!$item )
				{
					// Set the for tags and the values to replace.
					$item = str_replace( ']', '', $item ); // Had to replace this string because it was being a dick. Now I don't think I need the additional check
					$for_tags .= '['.$array_tag.'.'.$item.'],';
					$for_values .= $row[$item] . '^';
				}
					
			}
			// Explode the strings in array to pass through the str_replace
			$for_tags = explode ( ',', $for_tags );
			$for_values = explode ( '^', substr ( $for_values, 0, -1 ) );
			
			foreach ( $for_tags as $t )
			{
				if ( !in_array ( $t, $new_for_tags ) && !!$t )
					array_push ( $new_for_tags, $t );
			}
			
			// Build the content up
			
			$content .= str_replace( $new_for_tags, $for_values, $_loop_content );
			
			foreach ( $new_for_tags as $tg )
			{
				$this->_main_content = str_replace ( $tg, '', $this->_main_content );
			}
			
		}
		
		// Set the content

		$this->_main_content = preg_replace ( '#\\[FOREACH ' . $array . ' as ' . $array_tag . '](.+)\\[/' . $array . ' FOREACH\\]#s', $content, $this->_main_content );
		
	}

	/**
	 * Display the template.
	 */
	 
	private function render ()
	{
		echo $this->_main_content;	
	}
	
	private function __clone () { }

}

?>