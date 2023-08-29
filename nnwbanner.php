<?php
/**
 * Plugin Name: NNW Popup
 * Description: Un simple widget flotante para mostrar un video de YouTube.
 */
                                                                                
/*
 xkkkkkkkkkkkk:   okkkkkkkkkkkkkl                                               
cllllllllllllll. ;lllllllllllllll;                                              
llll.       :ll. ;lll.       ;lll' 'MMM. 'MMM     'MMM:  'MMM  KMM.         :MMM
llll        ;ll. ;lll.       'lll' 'MMM; 'MMM     'MMMO  'MMM  oMM;         dMMK
llll        ;ll. ;lll.       'lll' 'MMMd 'MMM     'MMMM. 'MMM  ,MMl  .WMo   KMMo
llll        ;ll. ;lll.       'lll' 'MMM0 'MMM     'MMMMo 'MMM   MMx  OMMW   MMM,
llll        ;l;':,cll.       'lll' 'MMMM.'MMM     'MMMMM.'MMM   OMX 'MMMM: cMMM 
lll.        ;;;..,;c;.        ;ll' 'MMMMo'MMM     'MMMMMo'MMM   cMM cMMMMO OMMO 
            ;.     .;.             'MMMMM'MMM     'MMMMMM'MMM   'MM'OMMMMM MMMc 
lkk:        '.     .'.        dkk' 'MMMOMdMMM     'MMMcMMdMMM    MMcMMMOMMcMMM' 
llll         'l  .c''        ;lll' 'MMMoMMMMM     'MMM.MMMMMM    OMMMMOcMMWMMM  
llll          .'o'.          'lll' 'MMM,MMMMM     'MMM kMMMMM    cMMMMc'MMMMMO  
llll          ,:c:           'lll' 'MMM.MMMMM     'MMM 'MMMMM    'MMMM' MMMMMc  
llll          ;lll.          'lll' 'MMM oMMMM     'MMM  oMMMM     NMMK  cMMMM.  
llll,         llll,          clll' 'MMM .MMMM     'MMM  .MMMM     0MMc  'MMMM   
;llllkkkkkkkkdlllllkkkkkkkkklllll. 'MMM  OMMM     'MMM   OMMM     kMM'   MMMO   
 ;llllllllllllllllllllllllllllll.  'MMM   OMM     'MMM    OMM     oMM    OMMc    */
                                                                                
                                                                             
// admnitracion
function youtube_widget_admin_menu() {
    add_options_page('NNW Popup', 'NNW Popup', 'manage_options', 'nnw-popup', 'youtube_widget_admin_page');
}

add_action('admin_menu', 'youtube_widget_admin_menu');

function youtube_widget_admin_page() {
    wp_enqueue_media();
    ?>
    <div class="wrap" style="text-align: center; max-width: 500px; margin: 50px auto; padding: 20px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
        <meta charset="UTF-8"> <!-- Aseguramos el reconocimiento de UTF-8 -->
        <img src="https://neonetwork.cl/MailFile/logo.png" alt="Logo" style="height: 50px; display: block; margin: 0 auto 20px auto;" />
        <form method="post" action="options.php">
            <?php
            settings_fields('nnw-popup-settings');
            do_settings_sections('nnw-popup');
            submit_button();
            ?>
        </form>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="https://neonetwork.cl" target="_blank">Creado por neonetwork.cl</a>
    </div>
    <script>
        jQuery(document).ready(function($){
            $('.nnw-image-upload-btn').click(function(e) {
                e.preventDefault();
                var custom_uploader = wp.media({
                    title: 'Seleccionar imagen',
                    button: {
                        text: 'Usar imagen'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('.nnw-image-upload').val(attachment.url);
                }).open();
            });
        });
    </script>
    <?php
}


function youtube_widget_settings() {
    register_setting('nnw-popup-settings', 'nnw_popup_video_link');
    register_setting('nnw-popup-settings', 'nnw_popup_bg_image');
    register_setting('nnw-popup-settings', 'nnw_popup_button_text');
    register_setting('nnw-popup-settings', 'nnw_popup_button_link');
    
    add_settings_section('nnw-popup-main-section', 'Configuraciones Principales', null, 'nnw-popup');
    add_settings_field('nnw-popup-video-link', 'Link del video de YouTube', 'youtube_widget_video_link_field', 'nnw-popup', 'nnw-popup-main-section');
    add_settings_field('nnw-popup-bg-image', 'Imagen de Fondo', 'youtube_widget_bg_image_field', 'nnw-popup', 'nnw-popup-main-section');
    add_settings_field('nnw-popup-button-text', 'Texto del bot車n', 'youtube_widget_button_text_field', 'nnw-popup', 'nnw-popup-main-section');
    add_settings_field('nnw-popup-button-link', 'Enlace del bot車n', 'youtube_widget_button_link_field', 'nnw-popup', 'nnw-popup-main-section');
}

add_action('admin_init', 'youtube_widget_settings');

function youtube_widget_video_link_field() {
    $option = get_option('nnw_popup_video_link');
    echo '<input type="text" name="nnw_popup_video_link" value="' . esc_attr($option) . '" />';
}

function youtube_widget_bg_image_field() {
    $option = get_option('nnw_popup_bg_image');
    echo '<input type="text" name="nnw_popup_bg_image" class="nnw-image-upload" value="' . esc_attr($option) . '" />';
    echo '<button class="nnw-image-upload-btn button">Subir/Seleccionar imagen</button>';
}

function youtube_widget_button_text_field() {
    $option = get_option('nnw_popup_button_text');
    echo '<input type="text" name="nnw_popup_button_text" value="' . esc_attr($option) . '" />';
}

function youtube_widget_button_link_field() {
    $option = get_option('nnw_popup_button_link');
    echo '<input type="text" name="nnw_popup_button_link" value="' . esc_attr($option) . '" />';
}

// Widget
function youtube_widget() {
    $video_link = get_option('nnw_popup_video_link');
    $bg_image = get_option('nnw_popup_bg_image');
    $embed_url = str_replace('watch?v=', 'embed/', $video_link);
    $embed_url .= '?autoplay=1'; // Añadimos autoplay

    ?>
    <meta charset="UTF-8">
    <style>
        .nnwPopup {
            width: 80%;
            max-width: 390px;
            height: auto; 
            position: fixed;
            bottom: 10px;
            right: 10px;
            display: block;
            z-index: 9999;
            background-image: url('<?php echo esc_url($bg_image); ?>');
            background-size: cover;
            background-position: center;
            padding: 5px;
        }
        .nnwPopup iframe {
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .nnwPopup-close {
            position: absolute;
            top: 0;
            right: 0;
            cursor: pointer;
            background-color: white;
            color: red;
            padding: 5px;
        }
        .nnwPopup-title {
            text-align: center;
            font-size: 24px;
            color: #fff;
            padding: 10px;
        }
        
        @media (max-width: 480px) {
            .nnwPopup {
                bottom: 5px;
                right: 5px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.nnwPopup-close').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('.nnwPopup').style.display = 'none';
                var iframe = document.querySelector('.nnwPopup iframe');
                iframe.src = iframe.src; // Esto detendrá el video
            });
        });
    </script>
    <div class="nnwPopup">
        <a href="#" class="nnwPopup-close">Cerrar</a>
        <div class="nnwPopup-title">120</div>
        <iframe src="<?php echo esc_url($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
    </div>
    <?php
}

add_action('wp_footer', 'youtube_widget');
?>