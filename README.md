# Random IP generator for private IPv4 networks

Generate a random private IPv4 subnet of specific size (bitmask),
or pick a random address from a defined private IPv4 network.


## Usage

Require the class in your own project using composer:

```
composer require gregorj/randomip
```

The static functions can be called easily in your code:

```php

//echos a random class B (172.[16-31].0.0/12) network for 6 hosts.
echo \GregorJ\RandomIP\RandomPrivateIPv4::randomNetwork('B', 29);

//echos a random IP address inside the network 192.168.21.0/24
echo \GregorJ\RandomIP\RandomPrivateIPv4::randomIP("192.168.21.0", 24);
```

## Restriction

This code is only suitable for private IPv4 networks.
Therefore the bitmask cannot be lower than the following:


| Class | Starting address | minimum bitmask |
| :---: | :--------------- | :-------------: |
|   A   | 10.0.0.0         |        8        |
|   B   | 172.16.0.0       |       12        |
|   C   | 192.168.0.0      |       16        |
