<?php

// Null values will automatically use the blade content being searched 

$blade_mapper = [
  // Fully controllable blade order calls
  // 'all' => [
  //   [
  //     'order' => 0,
  //     'blade' => 'theme::html_head.all'
  //   ],
  //   [
  //     'order' => 1,
  //     'blade' => 'theme::layout.navbar'
  //   ],
  //   [
  //     'order' => 2,
  //     'blade' => 'theme::layout.sidebar_left'
  //   ],
  //   [
  //     'order' => 3,
  //     'blade' => null
  //   ],
  //   [
  //     'order' => 4,
  //     'blade' => 'theme::layout.sidebar_right'
  //   ],
  //   [
  //     'order' => 5,
  //     'blade' => 'theme::layout.footer'
  //   ]
  // ],
  // Allowing the blade more control of internals
  'all' => [
    [
      'order' => 0,
      'blade' => 'theme::html_head.all'
    ],
    [
      'order' => 1,
      'blade' => 'theme::layout.navbar'
    ],
    [
      'order' => 3,
      'blade' => null
    ],
    [
      'order' => 5,
      'blade' => 'theme::layout.footer'
    ]
  ]
];