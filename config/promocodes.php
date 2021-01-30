<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Related Configuration
    |--------------------------------------------------------------------------
    |
    | Migration configuration parameters. You can change promotional codes
    | table name, user model, pivot table name and relation keys.
    |
    */

    'database' => [

        /*
        |--------------------------------------------------------------------------
        | Promocodes Table Name
        |--------------------------------------------------------------------------
        |
        | Table name for promotional codes. You can change it and set anything
        | you like but don't forget to update migrations table too.
        |
        */

        'promocodes_table' => 'promocodes',

        /*
        |--------------------------------------------------------------------------
        | User Model Class
        |--------------------------------------------------------------------------
        |
        | Class of the user model. Set the model which will be able to use
        | promotional codes. Also don't forget to use AppliesPromocodes trait on
        | your users model.
        |
        */

        'user_model' => \App\Models\User::class,

        /*
        |--------------------------------------------------------------------------
        | Pivot Table Name
        |--------------------------------------------------------------------------
        |
        | Table name for pivot relation. This is a combination of promotional codes
        | table and users table. You can change it but don't forget to update
        | migrations file too.
        |
        */

        'pivot_table' => 'promocode_user',

        /*
        |--------------------------------------------------------------------------
        | Foreign Key Name
        |--------------------------------------------------------------------------
        |
        | Pivot key which is foreign key for promotional codes table to users table.
        | If you are using other table then "promocodes" for promotional codes model,
        | you can change it. Don't forget to update migrations too.
        |
        */

        'foreign_pivot_key' => 'promocode_id',

        /*
        |--------------------------------------------------------------------------
        | Related Key Name
        |--------------------------------------------------------------------------
        |
        | Pivot key which is relating promotional codes table to users table. If
        | you are using other table then "users" for user model, you can change it.
        | Don't forget to update migrations too.
        |
        */

        'related_pivot_key' => 'user_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Characters for Generation
    |--------------------------------------------------------------------------
    |
    | Following characters will be used for code generation. You can use
    | any character you would like, but try to avoid visually similar ones,
    | like O (letter) and 0 (number).
    |
    */

    'characters' => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',

    /*
    |--------------------------------------------------------------------------
    | Global Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix will be added in the beginning of each promotional code. It will be
    | separated with delimiter value from configuration. This is a global parameter,
    | but you can also set prefix per code.
    |
    */

    'prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Global Suffix
    |--------------------------------------------------------------------------
    |
    | Suffix will be added in the end of each promotional code. It will be
    | separated with delimiter value from configuration. This is a global parameter,
    | but you can also set prefix per code.
    |
    */

    'suffix' => null,

    /*
    |--------------------------------------------------------------------------
    | Code Mask
    |--------------------------------------------------------------------------
    |
    | Mask for promotional code generation. Random characters will be set
    | instead of asterisk symbols. Any other symbol will not be touched.
    | So you can generate patterns for promotional codes.
    |
    */

    'mask' => '****-****',

    /*
    |--------------------------------------------------------------------------
    | Suffix / Prefix Delimiter
    |--------------------------------------------------------------------------
    |
    | Delimiter for promotional code prefix and suffix. This will be used
    | to separate prefix and suffix from middle part of promotional code.
    | You can leave it as empty string or set anything you would like.
    |
    */

    'delimiter' => '-',

];
