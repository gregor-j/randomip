<?php
/**
 * GregorJ/RandomIP/RandomPrivateIPv4.php
 *
 * Generate a random private IPv4 subnet of specific size (bitmask),
 * or pick a random address from a defined private IPv4 network.
 *
 * PHP version 5
 *
 * @category StaticClass
 * @package  RandomIP
 * @author   GregorJ
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/gregor-j/randomip Repository
 */

namespace GregorJ\RandomIP;

use Leth\IPAddress\IP;

/**
 * GregorJ\RandomIP\RandomPrivateIPv4
 *
 * Generate a random private IPv4 subnet of specific size (bitmask),
 * or pick a random address from a defined private IPv4 network.
 *
 * @category StaticClass
 * @package  RandomIP
 * @author   GregorJ
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/gregor-j/randomip Repository
 */
class RandomPrivateIPv4
{

    /**
     * Generate a random private network address.
     *
     * @param string $class Wich private network class: A, B or C
     * @param int    $bits  The bitmask of the desired network: 8-30
     *
     * @return string A random network range in format <network address>/<bits>
     * @throws \UnexpectedValueException in case the given class is not A, B or C,
     *         or the number of bits don't fit the given class.
     */
    public static function randomNetwork($class, $bits)
    {
        //get the minimum bit mask for the given class
        $min_mask_bits = self::networkMaskBits($class);

        //validate the bits parameter
        $mask_bits = filter_var($bits, FILTER_VALIDATE_INT);
        if (false === $mask_bits) {
            throw new \UnexpectedValueException(
                "The bits parameter has to be an integer."
            );
        }

        //the bit mask has to be above the minimum bitmask for the given network
        if ($mask_bits < $min_mask_bits) {
            throw new \UnexpectedValueException(
                sprintf(
                    "A class %s network has at least %u mask bits.",
                    $class,
                    $min_mask_bits
                )
            );
        }

        $network_start = self::networkStartAddress($class);

        //catch the case where there is only one possible subnet
        if ($mask_bits == $min_mask_bits) {
            $return = $network_start;
        } else {
            $random_ip = self::randomIP($network_start, $min_mask_bits);
            $network = IP\NetworkAddress::factory($random_ip, $mask_bits);
            $return = (string) $network->get_network_start();
        }

        return sprintf("%s/%u", $return, $mask_bits);
    }

    /**
     * Choose a random ip address within a network.
     *
     * @param string $network The network address of the network.
     * @param int    $bits    The bitmask of the network.
     *
     * @return string A random ip address within the given network.
     */
    public static function randomIP($network, $bits)
    {
        $net = IP\NetworkAddress::factory(sprintf("%s/%u", $network, $bits));
        $host_count = $net->count();
        $random_host = rand(1, ($host_count - 2));
        $return = (string) $net->get_address_in_network($random_host);
        return $return;
    }

    /**
     * Returns the starting address of a private network class.
     *
     * @param string $class Wich private network class: A, B or C
     *
     * @return string The starting network address for the given class in
     *         format <network address>/<bitmask>
     * @throws \UnexpectedValueException in case the given class is not A, B or C.
     */
    public static function networkStartAddress($class)
    {
        switch ($class) {
        case 'A':
            $return = '10.0.0.0';
            break;
        case 'B':
            $return = '172.16.0.0';
            break;
        case 'C':
            $return = '192.168.0.0';
            break;
        default:
            throw new \UnexpectedValueException(
                "Unknown private network class. Choose either A, B or C!"
            );
        }
        return $return;
    }

    /**
     * Returns the network mask bits for a given private network class.
     *
     * @param string $class Wich private network class: A, B or C
     *
     * @return int The number of bits masked in the given private network class.
     * @throws \UnexpectedValueException in case the given class is not A, B or C.
     */
    public static function networkMaskBits($class)
    {
        switch ($class) {
        case 'A':
            $return = 8;
            break;
        case 'B':
            $return = 12;
            break;
        case 'C':
            $return = 16;
            break;
        default:
            throw new \UnexpectedValueException(
                "Unknown private network class. Choose either A, B or C!"
            );
        }
        return $return;
    }

}
