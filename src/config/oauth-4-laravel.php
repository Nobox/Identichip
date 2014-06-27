<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => 'Session',

    /**
     * Consumers
     */
    'consumers' => array(

        /**
         * Facebook
         */
        'Facebook' => array(
            'client_id'     => '1427862577494542',
            'client_secret' => '19d1e5293b9a766c821773f7121e0e22',
            'scope'         => array('email'),
        ),

    )

);