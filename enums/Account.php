<?php

namespace Enumerate;
use Enumerate\Extension\BackedEnumTrait;

enum Account: string 
{
    use BackedEnumTrait;
    
    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';
}
