<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\AccessObject;
use Dtkahl\AccessControl\AccessRole;
use Dtkahl\AccessControl\Judge;

class AccessControlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Judge
     */
    private $judge;

    public function setUp()
    {
        $user = new TestUser(["member"]);

        $role_member = new AccessRole("member", ["access", "test"], [
            "blog" => ["view"]
        ]);

        $role_author = new AccessRole("author", ["write"], [
            "comment" => ["write", "remove"] // TODO extend subscriber
        ]);
        $role_subscriber = new AccessRole("subscriber", [], [
            "comment" => ["write"]
        ]);
        $role_creator = new AccessRole("creator", ["edit", "remove"]);

        $object_blog = new AccessObject("blog", [$role_author, $role_subscriber]);
        $object_comment = new AccessObject("comment", [$role_creator]);

        $this->judge = new Judge([$role_member], [$object_blog, $object_comment], $user);
    }

    public function test()
    {
        $blog = new TestBlog(["author"], []);
        $comment = new TestComment([], [$blog]);

        // TODO test NotAllowedException on checkRight

        $this->assertTrue($this->judge->hasRight("access")); // positive global right
        $this->assertTrue($this->judge->hasRight(["access", "test"])); // positive multiple rights
        $this->assertTrue($this->judge->hasRight("write", $blog)); // positive object right
//        $this->assertTrue($this->judge->hasRight("view", $blog)); // positive object related right
//        $this->assertTrue($this->judge->hasRight("write", $comment)); // positive related object right
//        $this->assertTrue($this->judge->hasRight("remove", $comment)); // positive related object right
        $this->assertFalse($this->judge->hasRight("destroy")); // negative global right
        $this->assertFalse($this->judge->hasRight(["access", "destroy"])); // negative multiple rights
//        $this->assertFalse($this->judge->hasRight("destroy", $blog)); // negative object right
//        $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right
//        $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right

        $this->assertTrue($this->judge->hasRole("member"));
//        $this->assertTrue($this->judge->hasRole("author", $blog));
        $this->assertFalse($this->judge->hasRole("admin"));
//        $this->assertFalse($this->judge->hasRole("creator", $comment));
    }

}