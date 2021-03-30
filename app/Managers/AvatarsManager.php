<?php
namespace App\Managers;
define("GETAVATAAARS_DATA",[
                            'avatarStyle'       => ['Transparent'],
                            'facialHair'        => ['Blank'],
                            'accessoriesType'   => ['Blank'],
                            'skinColor'         => ['Light'],
                            'clotheColor'       => ['Black', 'Heather', 'Blue01', 'Blue02', 'Blue03', 'Gray02', 'Red'],
                            'mouthType'         => ['Default', 'Smile', 'Twinkle'],
                            'eyeType'           => ['Default', 'Happy'],
                            'eyebrowType'       => ['Default', 'DefaultNatural'],
                            'clotheType'        => ['BlazerShirt', 'BlazerSweater', 'Hoodie', 'CollarSweater', 'ShirtCrewNeck'],
                            'topType'           => ['WinterHat2', 'Hat', 'ShortHairTheCaesarSidePart', 'ShortHairShortFlat', 'LongHairStraight']
                        ]);
class AvatarsManager{
    public static function getAvatar($user_id){
        $avatar = app('db')->select("SELECT avatar FROM users
                                        WHERE id = :user_id",
                                        ['user_id' => $user_id]);
        $avatar = empty($avatar) ? null : $avatar[0]->avatar;
        return $avatar;
    }

    public static function setAvatar($user_id, $avatar){
        app('db')->update("UPDATE users
                        SET avatar = :avatar
                        WHERE id = :user_id",
                        ['user_id' => $user_id, 'avatar' => $avatar]);
    }

    public static function getAvataaars(){
        $avatar = "https://avataaars.io/?";
        $i = 0;
        $length = count(GETAVATAAARS_DATA);
        foreach(GETAVATAAARS_DATA as $param => $values){
            $i++;
            $avatar .= $param."=".$values[array_rand($values, 1)];
            if($i < $length){
                $avatar .= "&";
            }
        }
        return $avatar;
    }

    public static function getGravatar($email){
      $avatar = null;
      $hash = md5(strtolower(trim($email)));
      $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404&s=200';
      $headers = @get_headers($uri);

      if (preg_match("|200|", $headers[0])) {
        $avatar = $uri;
      }
      return $avatar;
    }

}

?>
