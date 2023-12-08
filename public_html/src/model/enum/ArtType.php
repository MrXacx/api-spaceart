<?php

namespace App\Model\Enumerate;

/**
 * Enumeração de tipo de arte
 * @package App\Model\Enumerate
 * @author Ariel Santos <MrXacx>
 */
enum ArtType: string
{
    case SONG = 'music';
    case SCULPTURE = 'sculpture';
    case PAINTING = 'pintura';
    case DANCE = 'dance';
    case ACTING = 'acting';

}

?>