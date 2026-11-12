<?php

/*
 * The 14 starter templates, transcribed from the original desk bundle
 * (docs/superpowers/reference/crystal-creek/page-1XroWPtX.js, array `j`).
 * `company` is the original company index: 0 Air, 1 Pest Control, 2 Roofing, 3 Texas Home Maintenance Pros.
 */

$line = fn (string $name, string $unit = 'job', ?float $fixed = null, float $qty = 1) => [
    'name' => $name, 'qty' => $qty, 'unit' => $unit, 'labor' => 0, 'materials' => 0, 'fixed' => $fixed,
];

$tiers = [];
foreach (['Essential', 'Plus', 'Premier'] as $i => $tier) {
    $tiers[] = [
        'key' => 'p'.$i,
        'title' => $tier.' quarterly pest plan',
        'company' => 1,
        'service' => 'Pest plan',
        'scope' => 'Initial general pest service for the selected quarterly plan. Confirm covered pests and service areas before issuing.',
        'lines' => [$line('Initial service · quarterly enrollment promo', 'visit', 150)],
        'terms' => 'This total covers the initial service only. Enter the recurring quarterly fee and service terms here before issuing. Additional treatment discount for this tier is '.[10, 15, 20][$i].'%.',
    ];
}

$addOns = [];
foreach ([
    ['Mosquito', 75], ['Rodent inspection and trapping', 200], ['Flea and tick', 350], ['Wasp or hornet', 150],
    ['Fire ant yard treatment', 150], ['Bed bug treatment per room', 350], ['Carpenter ant', 200], ['Carpenter bee', 175],
] as [$name, $price]) {
    $addOns[] = $line($name, $name === 'Bed bug treatment per room' ? 'room' : 'visit', $price, 0);
}

return [
    [
        'key' => 't1', 'title' => 'Evaporator coil replacement', 'company' => 0, 'service' => 'HVAC repair',
        'scope' => 'Replace the evaporator coil, pressure test, evacuate, and verify cooling operation.',
        'lines' => [$line('Evaporator coil labor', 'job', 1800), $line('Coil or warranty allowance'), $line('Refrigerant', 'lb'), $line('Access or carpentry')],
        'terms' => 'Confirm warranty eligibility and access requirements. Price refrigerant and access separately when applicable.',
    ],
    [
        'key' => 't2', 'title' => 'Condenser coil replacement', 'company' => 0, 'service' => 'HVAC repair',
        'scope' => 'Replace the condenser coil, leak test, evacuate, recharge and test operation.',
        'lines' => [$line('Condenser coil labor', 'job', 1500), $line('Coil or warranty allowance'), $line('Refrigerant', 'lb')],
        'terms' => '',
    ],
    [
        'key' => 't3', 'title' => 'Complete HVAC system replacement', 'company' => 0, 'service' => 'HVAC replacement',
        'scope' => 'Remove existing equipment, install matched replacement equipment, and commission the system.',
        'lines' => [$line('Matched condenser and indoor equipment'), $line('Installation labor', 'hr'), $line('Drain, float switch and connection materials'), $line('Disposal and startup')],
        'terms' => 'Confirm system size, equipment model, permit requirements and warranty.',
    ],
    ...$tiers,
    [
        'key' => 'p3', 'title' => 'German cockroach treatment', 'company' => 1, 'service' => 'Pest treatment',
        'scope' => 'Inspect and treat affected areas. Confirm infestation severity and preparation requirements.',
        'lines' => [$line('Starting treatment price', 'job', 550)],
        'terms' => 'Starting rate. Confirm follow-up frequency and the final scope after inspection.',
    ],
    [
        'key' => 'p4', 'title' => 'Pest add-on treatment menu', 'company' => 1, 'service' => 'Pest treatment',
        'scope' => 'Select the requested treatments. Remove unused rows or set their quantity to zero.',
        'lines' => $addOns,
        'terms' => '',
    ],
    [
        'key' => 'r1', 'title' => 'Roof replacement scope', 'company' => 2, 'service' => 'Roofing',
        'scope' => 'Tear off and dispose of existing roofing. Install specified underlayment, shingles, flashing and ventilation. Clean up and magnet-sweep the site.',
        'lines' => [$line('Tear-off and installation', 'sq'), $line('Shingles and waste allowance', 'sq'), $line('Underlayment and starter', 'sq'), $line('Drip edge and flashing', 'lf'), $line('Decking allowance', 'sheet'), $line('Permit or inspection allowance')],
        'terms' => 'Identify decking allowance and any applicable windstorm inspection requirements before approval.',
    ],
    [
        'key' => 'm1', 'title' => 'Hardie lap siding replacement', 'company' => 3, 'service' => 'Siding',
        'scope' => 'Remove designated siding, inspect exposed sheathing, install house wrap and flashing, and install primed lap siding and trim.',
        'lines' => [$line('Tear-off and installation', 'sq ft'), $line('Fiber cement siding', 'sq ft'), $line('House wrap and flashing', 'sq ft'), $line('Exterior trim', 'lf'), $line('Debris haul-off')],
        'terms' => 'Painting and concealed rot repairs are excluded unless itemized.',
    ],
    [
        'key' => 'm2', 'title' => 'Rental make-ready', 'company' => 3, 'service' => 'Make-ready',
        'scope' => 'Complete approved turnover repairs. Confirm the work list from the move-out inspection and document completion.',
        'lines' => [$line('Patch and paint', 'room'), $line('Hardware and fixture repairs'), $line('Turnover cleaning'), $line('Trash-out and disposal'), $line('Final walkthrough')],
        'terms' => '',
    ],
    [
        'key' => 'm3', 'title' => 'Move-out or restoration cleaning', 'company' => 3, 'service' => 'Cleaning',
        'scope' => 'Clean agreed rooms, fixtures and surfaces. Document heavy soil or restoration needs separately.',
        'lines' => [$line('Standard turnover cleaning', 'job', 310), $line('Restoration cleaning add-on', 'hr'), $line('Supplies and disposal')],
        'terms' => '',
    ],
    [
        'key' => 'm4', 'title' => 'Plumbing repair', 'company' => 3, 'service' => 'Plumbing',
        'scope' => 'Identify the source, complete the approved repair and test for leaks and normal operation.',
        'lines' => [$line('Diagnostic or repair labor', 'hr'), $line('Fixture or replacement parts'), $line('Access and restoration allowance')],
        'terms' => '',
    ],
    [
        'key' => 'm5', 'title' => 'General maintenance service', 'company' => 3, 'service' => 'General maintenance',
        'scope' => 'Complete the listed service tasks and document the work performed.',
        'lines' => [$line('Technician labor', 'hr'), $line('Repair materials'), $line('Disposal or equipment allowance')],
        'terms' => '',
    ],
];
