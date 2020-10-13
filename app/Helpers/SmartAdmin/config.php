<?php
// register our UI plugins
SmartUI::register( 'widget', 'Widget' );
SmartUI::register( 'datatable', 'DataTable' );
SmartUI::register( 'button', 'Button' );
SmartUI::register( 'tab', 'Tab' );
SmartUI::register( 'accordion', 'Accordion' );
SmartUI::register( 'carousel', 'Carousel' );
SmartUI::register( 'smartform', 'SmartForm' );
SmartUI::register( 'nav', 'Nav' );

function admin_nav_array()
{
    return config( 'admin_menu' );
}
