/* ******Public Video****** */


$('#public-submit').click(() => {
    get_public_video();
});


async function get_public_video()
{
    $('.public-video-items').remove();
    $('#public-video-info').css('display', 'none');


    const url = $('#url').val();


    if(url === '')
    {
        $('#invalid-url').css({'opacity': '1', 'visibility': 'visible'});
        $('#invalid-url').find('span').text('Please Enter URL.');
    }
    else if(!valid_url(url))
    {
        $('#invalid-url').css({'opacity': '1', 'visibility': 'visible'});
        $('#invalid-url').find('span').text('URL is not valid. Please check again.');
    }
    else
    {
        $('#invalid-url').css({'opacity': '0', 'visibility': 'hidden'});
        $('#invalid-url').find('span').text('');


        $('#public-submit').css({'--animation-state': 'running', '--animation-duration': '2s'});
        $('#public-submit').prop('disabled', true);
        $('#public-submit').text('Analyzing...');
    }


    // Ajax
    $.ajax({
        type: "POST",
        url: "apps/public_video/api.php",
        dataType: "json",
        data: {
            url: url,
        },
        success: (response) => {
            $('#public-submit').css({'--animation-state': 'paused', '--animation-duration': '0s'});
            $('#public-submit').prop('disabled', false);
            $('#public-submit').text('Submit');


            $('#public-video-info').css('display', 'block');


            let elements = '';
            const links = response.links;
            Object.keys(links).forEach((key) => {
            elements = `<li class="public-video-items">
                        <span class="label">${key}</span>
                        <a href="${links[key]}&dl=1" class="btn btn-download" role="button">
                            <span>Download</span>
                        </a>
                        <a nohref data-href="${links[key]}" role="button" class="btn btn-preview" data-bs-toggle="modal" data-bs-target="#modal">
                            <span>Preview</span>
                        </a>
                        </li>`;


                $('#public-video-list').append(elements).css('text-transform', 'capitalize');
            });


            $('.public-video-title').text(response.title);


            window.scrollTo(0, document.body.scrollHeight);
        }
    });
}


// Check URL
function valid_url(value)
{
    let pattern = /^(?:https:\/\/)?(?:fb.watch\/|www\.facebook\.com\/)?(?:[\w\d].*\/)?(?:videos\/)?(?:[\w\d]*\/)?$/;

    return pattern.test(value);
}


// Typing Effect
function typing(txt, speed)
{
    let chars = txt.split('');
    let placeholder = '';
    chars.forEach((char, index) => 
    {
        setTimeout(() => {
            placeholder += char;
            document.getElementById('url').setAttribute('placeholder', placeholder);
        }, speed * index);
    });
}


function random_text()
{
    let text_array = ["https://www.facebook.com/100005493240661/videos/1269306543474989/", "https://fb.watch/atlG2c4rlS/"];

    let random = Math.floor(Math.random() * text_array.length);

    return "Eg:" + text_array[random];
}


typing(random_text(), 50);


// Public Preview
$('#public-video-list').on('click', '.btn-preview', function () {
    let href = $(this).data('href');
    let patt = /mp4|webm/;
    let type = href.match(patt);
    $('.video').find('source').attr({'src': href, 'type': `video/${type}`});
    $('.video').find('video')[0].load();
});


/* ******Public Video****** */


/******************************/ 


/* ******Private Video****** */


// Private Download
$('#private-url').keydown(() => { 
    let private_url = $('#private-url').val();
    
    $('#private-source-url').val(`view-source:${private_url}`);
});


$('#btn-copy').click(() => {
    $('#btn-copy').text('Copied!');
    $('#private-source-url').select();
    document.execCommand('copy');

    setTimeout(() => {
        $('#btn-copy').text('Copy');
    }, 1000);
});



$('#private-submit').click(() => {
    get_private_video();
});


async function get_private_video()
{
    $('.private-video-items').remove();
    $('#private-video-info').css('display', 'none');


    $('#private-submit').css({'--animation-state': 'running', '--animation-duration': '2s'});
    $('#private-submit').prop('disabled', true);
    $('#private-submit').text('Analyzing...');


    // Get File Data
    const file_data = $('#file').prop('files')[0];
    const form_data = new FormData();
    form_data.append('file', file_data);


    // Ajax
    $.ajax({
        type: "POST",
        url: "apps/private_video/api.php",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: (response) => {
            $('#invalid-file').css({'opacity': '0', 'visibility': 'hidden'});
            $('#invalid-file').find('span').text('');


            $('#private-submit').css({'--animation-state': 'paused', '--animation-duration': '0s'});
            $('#private-submit').prop('disabled', false);
            $('#private-submit').text('Submit');


            $('#private-video-info').css('display', 'block');


            let elements = '';
            const links = response.links;
            Object.keys(links).forEach((key) => {
                elements = `<li class="private-video-items">
                        <span class="label">${key}</span>
                        <a href="${links[key]}&dl=1" class="btn btn-download" role="button">
                            <span>Download</span>
                        </a>
                        <a nohref data-href="${links[key]}" role="button" class="btn btn-preview" data-bs-toggle="modal" data-bs-target="#modal">
                            <span>Preview</span>
                        </a>
                        </li>`;


                $('#private-video-list').append(elements).css('text-transform', 'capitalize');
            });


            $('.private-video-title').text(response.title);


            window.scrollTo(0, document.body.scrollHeight);
        },
        error: (response) => {
            $('#private-submit').css({'--animation-state': 'paused', '--animation-duration': '0s'});
            $('#private-submit').prop('disabled', false);
            $('#private-submit').text('Submit');


            $('#invalid-file').css({'opacity': '1', 'visibility': 'visible'});
            $('#invalid-file').find('span').text(response.responseJSON.error);
        }
    });
}


// Private Preview
$('#private-video-list').on('click', '.btn-preview', function () {
    let href = $(this).data('href');
    let patt = /mp4|webm/;
    let type = href.match(patt);
    $('.video').find('source').attr({'src': href, 'type': `video/${type}`});
    $('.video').find('video')[0].load();
});


/* ******Private Video****** */


/******************************/ 


/* ******Merge Video****** */


$('#merge-submit').click(() => {
    merge_video();
});


async function merge_video()
{
    $('#merge-submit').css({'--animation-state': 'running', '--animation-duration': '2s'});
    $('#merge-submit').prop('disabled', true);
    $('#merge-submit').text('Analyzing...');

    $('#invalid-video-file').css({'opacity': '0', 'visibility': 'hidden'});
    $('#invalid-video-file').find('span').text('');


    // Get File Data
    const file_1_data = $('#video-file-1').prop('files')[0];
    const file_2_data = $('#video-file-2').prop('files')[0];
    const form_data = new FormData();
    form_data.append('video_1', file_1_data);
    form_data.append('video_2', file_2_data);

    
    // Ajax
    $.ajax({
        type: "POST",
        url: "apps/merge_video/merge.php",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: (response) => {
            $('#merge-submit').css({'--animation-state': 'paused', '--animation-duration': '0s'});
            $('#merge-submit').prop('disabled', false);
            $('#merge-submit').text('Submit');


            if($('#invalid-video-file').hasClass('alert-danger'))
            {
                $('#invalid-video-file').removeClass('alert-danger').addClass('alert-success');
            }


            $('#invalid-video-file').css({'opacity': '1', 'visibility': 'visible'});
            $('#invalid-video-file').find('span').text(response.success);
        },
        error: (response) => {
            $('#merge-submit').css({'--animation-state': 'paused', '--animation-duration': '0s'});
            $('#merge-submit').prop('disabled', false);
            $('#merge-submit').text('Submit');


            if($('#invalid-video-file').hasClass('alert-success'))
            {
                $('#invalid-video-file').removeClass('alert-success').addClass('alert-danger');
            }


            $('#invalid-video-file').css({'opacity': '1', 'visibility': 'visible'});
            $('#invalid-video-file').find('span').text(response.responseJSON.error);
        }
    });
}


/* ******Merge Video****** */