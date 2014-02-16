<?php

/**
 * Get post id from a object.
 *
 * @param object $post
 * @since 1.0
 *
 * @return int
 */

function get_ptb_post_id ($post) {
  if (is_object($post)) {
    return $post->ID;
  }

  return $post;
}

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @since 1.0
 *
 * @return string
 */

function get_ptb_page_new_url ($page_type) {
  return get_admin_url() . 'post-new.php?post_type=page&page_type=' . $page_type;
}

/**
 * Get page view from query string.
 *
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_page_view () {
  if (isset($_GET['page']) && strpos($_GET['page'], 'ptb') !== false) {
    return str_replace('ptb-', '', $_GET['page']);
  }

  return null;
}

/**
 * Get page type from query string.
 *
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_page_type () {
  if (isset($_GET['page_type']) && !empty($_GET['page_type'])) {
    return $_GET['page_type'];
  }

  return null;
}

/**
 * Get class name from php file.
 *
 * @param string $file
 * @since 1.0
 *
 * @return string|null
 */

function get_ptb_class_name ($file) {
  header('Content-Type: text/plain');
  $content = file_get_contents($file);
  $tokens = token_get_all($content);
  $class_token = false;
  $class_name = null;

  foreach ($tokens as $token) {
    if (is_array($token)) {
      if ($token[0] === T_CLASS) {
        $class_token = true;
      } else if ($class_token && $token[0] === T_STRING) {
        $class_name = $token[1];
        $class_token = false;
      }
    }
  }

  return $class_name;
}

/**
 * Slugify the given string.
 *
 * @param string $str
 *
 * @return string
 */

function ptb_slugify ($str) {
  $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $str = strtolower($str);
  $str = preg_replace("/\W/", '-', $str);
  $str = preg_replace("/-+/", '-', $str);
  return trim($str, '-');
}

/**
 * Ptbify the given string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function ptbify ($str) {
  if (!preg_match('/^ptb\_/', $str)) {
    return  'ptb_' . $str;
  }

  return $str;
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
 *
 * @return string
 */

function ptb_underscorify ($str) {
  return str_replace(' ', '_', str_replace('-', '_', $str));
}

/**
 * Remove `ptb-` or `ptb_` from the given string.
 *
 * @param string $str
 * @since 1.0
 *
 * @return string
 */

function ptb_remove_ptb ($str) {
  return str_replace('ptb-', '', str_replace('ptb_', '', $str));
}

/**
 * Get Html template for property type.
 *
 * @param string $type
 * @since 1.0
 *
 * @return string
 */

function ptb_get_html_for_type ($type) {
  return constant(PTB_Properties . '::' . $type . 'Html');
}

/**
 * Get page type for post id or post object.
 *
 * @param object|int $post_id
 * @since 1.0
 *
 * @return string|null
 */

function ptb_get_page_type ($post_id) {
  $post_id = get_ptb_post_id($post_id);

  $meta = get_post_meta($post_id, PTB_META_KEY, true);

  if (is_array($meta) && isset($meta['ptb_page_type'])) {
    return $meta['ptb_page_type'];
  }

  return null;
}

/**
 * Get properties array for page.
 *
 * @param object|int $post_id
 * @since 1.0
 *
 * @return array|null
 */

function ptb_get_properties ($post_id) {
  if (!isset($post_id)) {
    $post_id = get_the_ID();
  }
  $post_id = get_ptb_post_id($post_id);
  return get_post_meta($post_id, PTB_META_KEY, true);
}

/**
 * Get property value for property on a post.
 *
 * @param object|int $post_id
 * @param string $property
 * @param mixed $default
 * @since 1.0
 *
 * @return mixed
 */

function ptb_get_property_value ($post_id, $property, $default) {
  if (!isset($property)) {
    $property = $post_id;
    $post_id = get_the_ID();
  }
  
  $properties = ptb_get_properties($post_id);
  $property = ptb_underscorify(ptbify($property));

  if (is_array($post_meta) && isset($post_meta[$property])) {
    return $post_meta[$property];
  }

  return $default;
}

/**
 * Get the current page. Like in EPiServer.
 *
 * @param bool $array Return as array instead of object
 * @since 1.0
 *
 * @return object|array
 */

function current_page ($array) {
  $post_id = get_the_ID();
  $post = get_post($post_id, ARRAY_A);
  $post_meta = get_post_meta($post_id, PTB_META_KEY, true);
  if (is_array($post_meta)) {
    foreach ($post_meta as $key => $value) {
      $post[ptb_remove_ptb($key)] = $value;
    }
    return $array ? $post : (object)$post;
  }
  return null;
}