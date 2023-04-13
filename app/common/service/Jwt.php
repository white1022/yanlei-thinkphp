<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\exception\Unauthorized as UnauthorizedException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

/*
 * jwt
 * https://jwt.io/
 */
class Jwt
{
    /*
     * [issuer] jwt配置
     * 发布者的url地址
     */
    private $iss = 'issuer.com';

    /*
     * [subject] jwt配置
     * 该JWT所面向的用户，用于处理特定应用，不是常用的字段
     */
    private $sub;

    /*
     * [audience] jwt配置
     * 接受者的url地址
     */
    private $aud = 'audience.com';

    /*
     * [expiration] jwt配置
     * 该jwt销毁的时间；unix时间戳
     */
    private $exp;

    /*
     * [not before] jwt配置
     * 该jwt的使用时间不能早于该时间；unix时间戳
     */
    private $nbf;

    /*
     * [issued at] jwt配置
     * 该jwt的发布时间；unix 时间戳
     */
    private $iat;

    /*
     * [JWT ID] jwt配置
     * 该jwt的唯一ID编号
     */
    private $jti = '548efe48dfe543rt84';

    /*
     * 秘钥
     */
    private $secret = 'SD#$%#$dfg$%#44YL54df$%^DFGDdfF';

    /*
     * 表名称
     */
    private $tableName;

    /*
     * 表主键
     */
    private $tableId;

    /*
     * 令牌
     */
    private $token;

    /*
     * 从字符串解析
     * 使用解析器从JWT字符串创建一个新的令牌(以前面的令牌为例):
     */
    private $newToken;

    /*
     * 单例
     */
    private static $instance;

    /*
     * 私有化 __construct 函数
     */
    private function __construct()
    {
    }

    /*
     * 私有化 __clone 函数
     */
    private function __clone()
    {
    }

    /*
     * 获取单例
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new Jwt();
        }
        return self::$instance;
    }

    /*
     * 获取令牌
     */
    public function getToken() :string
    {
        return (string)$this->token;
    }

    /*
     * 设置令牌
     */
    public function setToken(string $token) :Jwt
    {
        $this->token = $token;
        return $this;
    }

    /*
     * 获取表名
     */
    public function getTableName() :string
    {
        return (string)$this->tableName;
    }

    /*
     * 设置表名
     */
    public function setTableName(string $table_name) :Jwt
    {
        $this->tableName = $table_name;
        return $this;
    }

    /*
     * 获取表主键
     */
    public function getTableId() :int
    {
        return (int)$this->tableId;
    }

    /*
     * 设置表主键
     */
    public function setTableId(int $table_id) :Jwt
    {
        $this->tableId = $table_id;
        return $this;
    }

    /*
     * 编码
     */
    public function encode() :Jwt
    {
        $time = time();
        $this->token = (new Builder())
            ->setHeader('alg', 'HS256')
            ->setIssuer($this->iss) // Configures the issuer (iss claim)
            ->setAudience($this->aud) // Configures the audience (aud claim)
            //->setId($this->jti, true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt($time) // Configures the time that the token was issued (iat claim)
            //->setNotBefore($time + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration($time + 12*60*60) // Configures the expiration time of the token (exp claim)
            ->set('table_name', $this->tableName) // Configures a new claim, called "uid"
            ->set('table_id', $this->tableId) // Configures a new claim, called "uid"
            ->sign(new Sha256(), $this->secret)
            ->getToken(); // Retrieves the generated token

        return $this;
    }

    /*
     * 解码
     */
    public function decode() :Token
    {
        try{
            if(!$this->newToken){
                $this->newToken = (new Parser())->parse((string)$this->token);
                $this->tableName = $this->newToken->getClaim('table_name');
                $this->tableId = $this->newToken->getClaim('table_id');
            }
            return $this->newToken;
        }catch (\Exception $e){
            throw new UnauthorizedException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 核实 使用签名来验证令牌在生成后是否被篡改
     */
    public function verify() :bool
    {
        return $this->decode()->verify(new Sha256(), $this->secret);
    }

    /*
     * 验证 令牌是否有效
     */
    public function validate() :bool
    {
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer($this->iss);  //验证iss
        $data->setAudience($this->aud); //验证aud
        //$data->setId($this->jti); //验证jti

        return $this->decode()->validate($data);
    }

}
