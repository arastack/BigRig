<?php

/*
| Health declaration options on the application form. Values are what is
| stored; labels are what applicants see. Shared by the web form, its
| validation and the printable PDF form.
*/

return [
    'options' => [
        'visual_impairment' => ['None', 'Long Sightedness', 'Short Sightedness', 'Color Blindness'],
        'hearing_aid' => ['None', 'BTE', 'ITE', 'RITE', 'ITC', 'CROS'],
        'physical_disability' => ['None', 'Ulcer', 'Cancer', 'HIV', 'Abdominal Pain'],
        'weed' => ['No', 'Yes'],
        'alcohol' => ['No', 'Casually', 'Often', 'Daily User'],
    ],

    'labels' => [
        'visual_impairment' => ['Color Blindness' => 'Colour blindness', 'Long Sightedness' => 'Long-sightedness', 'Short Sightedness' => 'Short-sightedness'],
        'hearing_aid' => ['BTE' => 'Behind the ear (BTE)', 'ITE' => 'In the ear (ITE)', 'RITE' => 'Receiver in the ear (RITE)', 'ITC' => 'In the canal (ITC)', 'CROS' => 'CROS / BiCROS'],
        'physical_disability' => ['HIV' => 'HIV/AIDS', 'Ulcer' => 'Ulcer', 'Abdominal Pain' => 'Chronic abdominal pain'],
        'alcohol' => ['No' => 'Never', 'Daily User' => 'Daily'],
    ],

    /*
    | Package discounts. A combo applies when the selection includes at least one
    | course from every group (courses are matched by a word in their name).
    */
    'combos' => [
        [
            'name' => 'CDL + Forklift Combo',
            'description' => 'Enrol for truck training (Standard or Automatic) together with Forklift Training and receive a ₦50,000 discount.',
            'groups' => [['CDL'], ['Forklift']],
            'discount' => 50000,
            'weeks' => 14,
        ],
    ],

    'relationships' => ['Parent', 'Spouse', 'Sibling', 'Child', 'Relative', 'Guardian', 'Friend', 'Employer'],
];
