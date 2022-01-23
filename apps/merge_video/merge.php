<?php

    // Check Files
    try
    {
        $video_1 = isset($_FILES['video_1']) ? $_FILES['video_1'] : '';
        $video_2 = isset($_FILES['video_2']) ? $_FILES['video_2'] : '';


        if(empty($video_1) || empty($video_2))
        {
            header('HTTP/1.0 404 Not Found');
            throw new Exception("Please Choose File.");
        }
        else
        {
            $tmp_1 = $video_1['name'];
            $tmp_2 = $video_2['name'];

            // Check Extension name
            $allowed = ['ogm', 'wmv', 'mpg', 'webm', 'ogv', 'mov', 'asx', 'mpeg', 'mp4', 'm4v', 'avi'];
            $ext_name_1 = pathinfo($tmp_1, PATHINFO_EXTENSION);
            $ext_name_2 = pathinfo($tmp_2, PATHINFO_EXTENSION);
            if(!in_array($ext_name_1, $allowed) || !in_array($ext_name_2, $allowed))
            {
                header('HTTP/1.0 404 Not Found');
                throw new Exception("Invalid File.");
            }
        }


        // Return JSON
        header('Content-Type: application/json');


        $tmp_1 = $video_1['tmp_name'];
        $tmp_2 = $video_2['tmp_name'];

        // Target Directory
        $target_dir = 'D:\Video';
        if(!file_exists($target_dir))
        {
            mkdir($target_dir, 0777, true);
        }

        
        $output = $target_dir ."\output-" . time() .".mp4";

        
        $command = "ffmpeg -i $tmp_1 -i $tmp_2 -c:v copy $output";

        system($command);

        $msg['success'] = 'Video merge successfully. Saved at D:/Video';
        echo json_encode($msg);
    }


    // Throw Error
    catch(Throwable $err)
    {
        $msg['error'] = $err->getMessage();
        echo json_encode($msg);
    }
?>