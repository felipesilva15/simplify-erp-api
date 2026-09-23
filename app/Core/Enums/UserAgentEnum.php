<?php 

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="UserAgentEnum",
 *   type="string",
 *   description="User agents:
 *      Firefox = 'FIREFOX'
 *      Chrome = 'CHROME'
 *      Edge = 'EDGE'
 *      Safari = 'SAFARI'
 *      Postman = 'POSTMAN'
 *      Insomnia = 'INSOMNIA'
 *      Bruno = 'BRUNO'
 *      Googlebot = 'GOOGLEBOT'
 *      Bot = 'BOT'
 *      Unknown = 'UNKNOWN'",
 *   enum={"FIREFOX", "CHROME", "EDGE", "SAFARI", "POSTMAN", "INSOMNIA", "BRUNO", "GOOGLEBOT", "BOT", "UNKNOWN"}
 * )
 */
enum UserAgentEnum: string
{
    case Firefox    = 'FIREFOX';
    case Chrome     = 'CHROME';
    case Edge       = 'EDGE';
    case Safari     = 'SAFARI';
    case Postman    = 'POSTMAN';
    case Insomnia   = 'INSOMNIA';
    case Bruno      = 'BRUNO';
    case Googlebot  = 'GOOGLEBOT';
    case Bot        = 'BOT';
    case Unknown    = 'UNKNOWN';

    public function label(): string
    {
        return match ($this) {
            self::Firefox   => 'Firefox',
            self::Chrome    => 'Chrome',
            self::Edge      => 'Edge',
            self::Safari    => 'Safari',
            self::Postman   => 'Postman',
            self::Insomnia  => 'Insomnia',
            self::Bruno     => 'Bruno',
            self::Googlebot => 'Google bot',
            self::Bot       => 'Crawler e bot',
            self::Unknown   => 'Desconhecido',
        };
    }
}