<?php
return array(


    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Consumers
     * Here add the different services config
     */
    'consumers' => array(

        /**
         * Facebook
         */
        'Facebook' => array(
            'client_id'     => '',
            'client_secret' => '',
            'scope'         => array(''),
        ),

        'Twitter' => array(
            'client_id'     => '',
            'client_secret' => '',
        ),

        'Google' => array(
            'client_id'     => '',
            'client_secret' => '',
            'app_name'      => '',
            'scope'         => array('', ''),
        )

    )
);
