<?php
/**
 * Plugin Name: NNW Popup
 * Description: Un simple widget flotante para mostrar un video de YouTube.
 */

// Administración
function youtube_widget_admin_menu() {
    add_options_page('NNW Popup', 'NNW Popup', 'manage_options', 'nnw-popup', 'youtube_widget_admin_page');
}

add_action('admin_menu', 'youtube_widget_admin_menu');

function youtube_widget_admin_page() {
    wp_enqueue_media();
    ?>
    <div class="wrap" style="text-align: center; max-width: 500px; margin: 50px auto; padding: 20px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
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
    add_settings_field('nnw-popup-button-text', 'Texto del botón', 'youtube_widget_button_text_field', 'nnw-popup', 'nnw-popup-main-section');
    add_settings_field('nnw-popup-button-link', 'Enlace del botón', 'youtube_widget_button_link_field', 'nnw-popup', 'nnw-popup-main-section');
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
    $embed_url .= '?autoplay=1';

    ?>
    <meta charset="UTF-8">
    <style>
        .nnwPopup {
            width: calc(100vh * 9/16);
            height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            display: block;
            z-index: 9999;
            background-image: url('<?php echo esc_url($bg_image); ?>');
            background-size: cover;
            background-position: center;
            padding: 0;
            overflow: hidden;
        }

        .nnwPopup iframe {
            position: absolute;
            width: calc(100% - 24px); 
            height: calc(100% - 162px); 
            top: 150px;
            bottom: 12px;
            right: 12px;
            left: 12px;
        }

        .nnwPopup-close {
            position: absolute;
            top: 100px;
            right: 20px;
            background-color: red; /* fondo rojo */
            color: white; /* letra blanca */
            border-radius: 50%;
            height: 30px;
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s; /* Transición suave al cambiar de color */
        }

        .nnwPopup-close:hover {
            background-color: white; /* al pasar el mouse: fondo blanco */
            color: red; /* al pasar el mouse: letra roja */
        }

        .playButton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 150px; /* Ajustado para que encaje el texto "Abrir entrevista" */
            height: 50px;
            background-image: url('<?php echo esc_url($bg_image); ?>');
            background-size: cover;
            background-position: center;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 25px;
            color: #FFF;
            font-size: 16px;
            text-align: center;
            line-height: 50px;
        }

        @media (max-width: 768px) { /* Estilos para móviles */
            .nnwPopup {
                width: 100vw;
                top: 0;
                right: 0;
                display: none; /* Hace que el widget comience cerrado en móviles */
            }

            .nnwPopup iframe {
                width: calc(100% - 24px); 
                top: 120px;
            }
            
            .nnwPopup-close {
                top: 70px;
            }
            
            .playButton {
                display: flex; /* Hace que el botón esté visible desde el inicio en móviles */
            }
        }
    </style>
    <div class="nnwPopup">
        <div class="nnwPopup-close">&#10005;</div>
        <iframe src="<?php echo esc_url($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="playButton">Abrir entrevista</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popup = document.querySelector('.nnwPopup');
            var closeButton = document.querySelector('.nnwPopup-close');
            var playButton = document.querySelector('.playButton');

            closeButton.addEventListener('click', function(e) {
                e.preventDefault();
                popup.style.display = 'none';
                playButton.style.display = 'flex';
                var iframe = document.querySelector('.nnwPopup iframe');
                iframe.src = iframe.src;
            });

            playButton.addEventListener('click', function() {
                popup.style.display = 'block';
                playButton.style.display = 'none';
            });
        });
    </script>
    <?php
}

add_action('wp_footer', 'youtube_widget');
?>