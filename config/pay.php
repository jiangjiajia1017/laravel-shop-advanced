<?php

return [
    'alipay' => [
        'app_id'         => '2016082000292048',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApn0SlNfwmFg8YE2bJ8NdqrNZOKFlIHrG1hjuVUcBt+M0D4ESp51+lOj5wjYpM4liCuVRf1FchHt7L09EONmCrYmKsvf21T/+XYAIWK1GPi0ugmcZiOqBjC3fDnHL+qQ5r2ctVB10FyPiXddqe7g1kWX6Cw6E55c4+MqjGI+7VCO4gQRhN0iCCkHFW4wyvuslAe9QwH4JxsN2VmjxufwbYyIRqbd4L50Ze+0iufMcQn8uBEHTlKZ1HY3/Qr1bJ8nE2DHmRuNbOrKUBWwegbXE2MpIwjHRHJFuJPVyQxYwKVRsu+fYbuco7RFxfFhLTvGT0lw67H/m6UPNPKs7oSRuvQIDAQAB',
        'private_key'    => 'MIIEpQIBAAKCAQEApb2siR7bGmP/J1xnBaMr++VNCQ+z2570D4/cuG7EgV+/vn3klaRiFd/LcmwqjlIViiDGwHtAt1w1Wbi8D47PgkOokpctoLrCHSkt8cHvWWR3iV6D65u0m89k4sgGrxhAxuF3d/M2SimpIWZmU4beYWStr8rxcvkGl2DJNrIyzBO0nBZwI1Zt21BO1ZULEFTod9JC6uRh/3rTRalfLYaQ8OCiAC+KRhESWQ2Hu3hi11gSFKpnXp7s7LNQ8phFhLNNz5Dp0o1HSbPNueGD+bvQgGRes8d2iW/e9UxZrwypn20i0ZIDbdYH6SwTLD27qZ46akamggTHVCUc2tlxKZGHLwIDAQABAoIBADTzlk3wp7uUuw4OPXPVn9XIeoYsmB/QdtUJ91CNQwa6Wn43pnIQpK2sZElYOXhizTLmKdmRByoNsZXKqXqm+7D4gMDkv9UcFaxPXbhhZ2C98lrW8Xqysk8dQXQax9fk/b0Mh6iw8WVcTga3unVxH1cqXTtOsTk0SmDOsTpiUExWc5T3SH2ASp71bXs4UbJfpT5ptLq3HcvgQqxSsojp6Kv1Zn0Mv2ndKikNzO31vi5qGYY91PhRFUfmLCudGu7l98qytM+VvipgS23gN3QS+q8VZxKJ4Nd1Gdjnr1zRo9/H558SrBv2SLB4ku0AD4YUgOREHfVbd9AtrgRi7MrqmgECgYEA2EGBssdqEKsTiQJdoAGho+N482k1xt9ktfe8GzuVWSjWF92iz/ON0VC4OS2mEYQuIJUrVxjPKhMBH7NsUiw3gv8cF0pGMsQMb53F6wJ7+0y3n/SRqPvtGJjRcgACh0MW/Dmk6m6ldERP8DBQwC3U1hqjLO9zxRTA7wG8gOwtTq8CgYEAxDOEzYmXm6BhAca5LgnRU1LzFRt4Q8p/6qFeYAvn9IlwswohX3JqwG5HZKmd1ukVKK9C3ptGUJ4ih37hiG+6PLV6eiFyBd+JtVUDvUy1MttM0EGO6vQd8EYcdrQ8YIDSxXQfh7BADDBTQVwviZQ9T/TnskkqREzF/UJGRjgRb4ECgYEAzXSu/GNo6Z0fWjPdL39dSo97AgcbCG6FCDztghIBukuJ2/K+FEOYoRzHacts04d5K4uNOZpgd+DGWI/mgctwkgw9bAMs3l5UUlbbCoO5tu8UJev910ZB4/SMSHqp7EhZNhiuDexBVUzxhYjhrzPb2e5EyRRWMQ+enXRQ20uHYBECgYEAlVfVxZHvGeHJFU4LlLSaZj9kknN3ZrqUBafRK3DEncmkRFP4tStlgJYwt7m4UGbY2UAWuVUd/61vAQ4eY/kPnLhSwvYEUd4mdyWAFC54Wn69CGRugf9RbpwffGeS8a39QZkzitgo2F5McRrXgHSa+uqjFn1Es/pwVmeFEFwLQYECgYEA0jS+sT3+h+FnjcJdnaZL/gi6kWM/b5Cb4RSlKl0U21wY7ZDLe7Gsmbpo3hMa78wzPMYO6AFMLQxf+sg6fGuqIHeBjeOenP1oXUO+G8Fo8bfe+bIFi7XDf3VIgjnJw54LFeEy8RDe5HAVf1wLQAXX1Lvf3/7i6J9Fj7iMhkwIJnU=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
