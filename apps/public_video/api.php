<?php

    require 'get_video_info.php';


    $patt = '/^(?:https:\/\/)?(?:fb.watch\/|www\.facebook\.com\/)?(?:[\w\d].*\/)?(?:videos\/)?(?:[\w\d]*\/)?$/';


    // Check URL
    try
    {
        $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';


        if(empty($_REQUEST['url']))
        {
            header('HTTP/1.0 404 Not Found');
            throw new Exception("Please Enter URL.");
        }
        else
        {
            $url = test_input($_REQUEST['url']);
            if(!preg_match($patt, $url))
            {
                header('HTTP/1.0 404 Not Found');
                throw new Exception("URL is not valid. Please check again.");
            }
        }


        // Return JSON
        header('Content-Type: application/json');
        $get_info = new get_info;
        $response = $get_info->get_response($url);
    

        echo json_encode($response);


    }


    // Throw Error
    catch(Throwable $err)
    {
      $msg['error'] = $err->getMessage();
      echo json_encode($msg);
    }


    // Test Input
    function test_input($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);

        return $value;
    }
?>