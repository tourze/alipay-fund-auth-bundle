<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    /**
     * 测试 ID 设置与获取
     */
    public function testSetAndGetId_withValidId_returnsId(): void
    {
        $id = '123456789';
        $this->account->setId($id);
        
        $this->assertEquals($id, $this->account->getId());
    }
    
    /**
     * 测试名称设置与获取
     */
    public function testSetAndGetName_withValidName_returnsName(): void
    {
        $name = 'Test Account';
        $this->account->setName($name);
        
        $this->assertEquals($name, $this->account->getName());
    }
    
    /**
     * 测试 AppID 设置与获取
     */
    public function testSetAndGetAppId_withValidAppId_returnsAppId(): void
    {
        $appId = 'test_app_123';
        $this->account->setAppId($appId);
        
        $this->assertEquals($appId, $this->account->getAppId());
    }
    
    /**
     * 测试 RSA 私钥设置与获取
     */
    public function testSetAndGetRsaPrivateKey_withValidKey_returnsKey(): void
    {
        $key = '-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANB\n-----END PRIVATE KEY-----';
        $this->account->setRsaPrivateKey($key);
        
        $this->assertEquals($key, $this->account->getRsaPrivateKey());
    }
    
    /**
     * 测试 RSA 公钥设置与获取
     */
    public function testSetAndGetRsaPublicKey_withValidKey_returnsKey(): void
    {
        $key = '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhki\n-----END PUBLIC KEY-----';
        $this->account->setRsaPublicKey($key);
        
        $this->assertEquals($key, $this->account->getRsaPublicKey());
    }
    
    /**
     * 测试有效性设置与获取
     */
    public function testSetAndIsValid_withTrue_returnsTrue(): void
    {
        $this->account->setValid(true);
        
        $this->assertTrue($this->account->isValid());
    }
    
    /**
     * 测试有效性设置与获取，使用 false
     */
    public function testSetAndIsValid_withFalse_returnsFalse(): void
    {
        $this->account->setValid(false);
        
        $this->assertFalse($this->account->isValid());
    }
    
    /**
     * 测试 __toString 方法
     */
    public function testToString_withNameAndId_returnsName(): void
    {
        $name = 'Test Account';
        $id = '123456789';
        
        $this->account->setId($id);
        $this->account->setName($name);
        
        $this->assertEquals($name, (string) $this->account);
    }
    
    /**
     * 测试 __toString 方法，没有 ID 时
     */
    public function testToString_withoutId_returnsEmptyString(): void
    {
        $this->account->setName('Test Account');
        
        $this->assertEquals('', (string) $this->account);
    }
    
    /**
     * 测试创建人设置与获取
     */
    public function testSetAndGetCreatedBy_withValidUser_returnsUser(): void
    {
        $user = 'admin';
        $this->account->setCreatedBy($user);
        
        $this->assertEquals($user, $this->account->getCreatedBy());
    }
    
    /**
     * 测试更新人设置与获取
     */
    public function testSetAndGetUpdatedBy_withValidUser_returnsUser(): void
    {
        $user = 'admin';
        $this->account->setUpdatedBy($user);
        
        $this->assertEquals($user, $this->account->getUpdatedBy());
    }
    
    /**
     * 测试创建时间设置与获取
     */
    public function testSetAndGetCreateTime_withDateTime_returnsDateTime(): void
    {
        $dateTime = new \DateTime();
        $this->account->setCreateTime($dateTime);
        
        $this->assertSame($dateTime, $this->account->getCreateTime());
    }
    
    /**
     * 测试更新时间设置与获取
     */
    public function testSetAndGetUpdateTime_withDateTime_returnsDateTime(): void
    {
        $dateTime = new \DateTime();
        $this->account->setUpdateTime($dateTime);
        
        $this->assertSame($dateTime, $this->account->getUpdateTime());
    }
    
    /**
     * 测试创建IP设置与获取
     */
    public function testSetAndGetCreatedFromIp_withValidIp_returnsIp(): void
    {
        $ip = '192.168.1.1';
        $this->account->setCreatedFromIp($ip);
        
        $this->assertEquals($ip, $this->account->getCreatedFromIp());
    }
    
    /**
     * 测试更新IP设置与获取
     */
    public function testSetAndGetUpdatedFromIp_withValidIp_returnsIp(): void
    {
        $ip = '192.168.1.1';
        $this->account->setUpdatedFromIp($ip);
        
        $this->assertEquals($ip, $this->account->getUpdatedFromIp());
    }
} 