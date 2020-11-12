<?php

namespace App\GraphQL\Queries;

use App\User;

class Developers
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $developers = User::where("email", "jluchavez@umindanao.edu.ph")->get();
        // throw new \RuntimeException(json_encode($developers), 1);
        return $developers;
    }
}
