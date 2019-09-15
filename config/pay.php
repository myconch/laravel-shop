<?php

return [
    'alipay' => [
        'app_id' => '2016100100640016',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAi0ojGqukZY1jT21CNy3ijIiL3vJx0DWLsF1K56TOPQzjlvKwFFqkR2co5L4OQd04Y06/gjPIJiDlMB9lIOTnBT0iIweJrBVXbpIRrDfTMylHkcgy63oqFlDaw5VZb3kCgap7Ho3+XpK5H26GFpE69Nbi1nDOtiTi4NS3uz7oWV4GnatQNG1QVOD75JwcNqHG31gJrU9/8sQO05gQlC61jQp6Sf++jI1b+TWg6jVj//ikWZTsub4mmS38um/nmxZf8rRfTwxcPtr1R5fb+cl2l3UGoDJ4F5O0LxSS+haiNNpNvsGuCRUrrm6zZF3kDHHMGJ3cyU0VZJeRe0pf6hPQ1wIDAQAB',
        'private_key' => 'MIIEogIBAAKCAQEAq726z2mNMb1yB2pJBlCS3w/c5jqhfsPXAtJrc2Jy/pjR5F0KTr2GRGBoD1z6n479+stUhip/J3Bhh/EGQYjbnm4m3Mi12cl7xrtxOTgyXmSMQ2EyCElW0qYDmfYTHgA2gjy+7n65BcXTqT0o+eZuH0Q6doCiGVdQP2D0ay9EpTZ0hBst7Qu3Jq1ZSKeLPBxQiie4hrzC8YXmKMOA/s2xOdErkDjWDNDDkNx1k3aMjulGclaA6vQp4R+QLdDg/ZaVgxMyBDFDG0ko6Q1axvqb2pMB7xpY62Wtw3+2rPxu7hFQIrCcwkVE/AY/lwQ0N2DCP+3jz67sWLBXFQjmpGiUhQIDAQABAoIBAG3w/X/90BeBcVfx4GGcfwkw/HaeHfgqop/52DN7WKZHakZIkWqqxSNMnBEJsp8mSMyeEbP5zICQ3r7JuvmFBSK83UAFdPTAmO413FuG8CehMnGH7SGE4YHkKPYMo1xXQo3JGWlP0l4xsDftISgpLWHyqYJnAl46mPKZvJ5JGiawUuz0l1KMTbCFbHL0Dvdtq+C+kphuqD7UxGnHZxrbQyAPLHDUJphZjh7FzgkqKjWNGwU9Yp1hf891cEYgKwtN6djr/kkNpNQhjSwTDviVKNrGp8RG2pVBnKFi6+gOZmthKLgA4wXx0oddI5XeWeI0xNON27obsdyzRKyaz2XwtdkCgYEA2aVWTxSfUDOrMlmgOoWgxfzQCsLg8ju8XCkwCU8bBKPVlVUN0BOd49OP1HgS5uDz6oIZqqFCWCgfRbv6vEXn0mj471M5huvNgTlahGk90dIxNArkE1OW6Br2Yzlj122mvWi0bNp4UWJwAkXsLwSN2hu8IEhvC9m1XaKPOLdZvAMCgYEAygF97YgpOxJHsiQAmol4jjXysi0+TvAEj7sW1iR5afHgOQfGKX7qUbvJWQAypw9vSMObEFY9TUfdKFZsiVRGymHvdEsjqE6KG87j4/ODsNtKthaZDoQx55Q1ItAyaxfbOel8ID4smjY2h/xmSPmrVezYhX8Tk8F/IjVycUfROtcCgYALZ3hwSFpYr3xm9P9KUbos5aRZDAERcEPcaGQV+MknoxYL7xr8Lir1xx4gOfJzmpHtVcfWgwCg8elBlCn9N4SAJ/PzRl8bTLvF66Vsjr8ogWUGnxN4V8bufosug9FRdnNoNVZO5dusGAZaeCN6CzkLlqxy3JjE/DFeqKfsht7y1QKBgCTnkt8DYiz1kP2dkDKrbMfmWTluBJUdSmgL0Qc8UKYnV0R0BCLumdZmzUkiPR7CNh2ABuM8LiThPSkyaM/KAsjUjY+cbp5AAwFDkeTCR0vXNFYB2OKLCib5r591k9B24kk5O8EUOsfNobbESNeKLWAcTg5NggEbd6ODSi4h5bqvAoGAKcvDvrrTcrX+jKaR8KyEc3ugisrmJ+gG4J/wRvu2iTbmf99eZqgOW8x/M+W1WaZ7s5R8WScDBgd7eyBpDmllH9WlY3JMLlki+FkjM57VE/6P1mQMZem0Zut826GP+8DhaCFwJT6WVjwOA326ODbbJb595iSa2r6a2J0dfLnRQ5Q=',
        'log' => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];