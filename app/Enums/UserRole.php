<?php

namespace App\Enums;

enum UserRole: string
{
    case Personnel = 'personnel';
    case ResponsablePersonnel = 'responsable_personnel';
    case ChefCentre = 'chef_centre';
    case Administrateur = 'administrateur';
}
