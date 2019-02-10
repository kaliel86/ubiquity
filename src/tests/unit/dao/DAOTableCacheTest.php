<?php
use Ubiquity\orm\DAO;
use models\User;
use models\Organization;
use Ubiquity\db\Database;
use models\Groupe;
use Ubiquity\cache\database\TableCache;

/**
 * DAO test case.
 */
class DAOTableCacheTest extends BaseTest {

	/**
	 *
	 * @var DAO
	 */
	private $dao;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function _before() {
		parent::_before ();
		$this->dao = new DAO ();
		$this->_loadConfig ();
		$this->config ["database"] ["cache"] = TableCache::class;
		$this->_startCache ();
		$this->_startDatabase ( $this->dao );
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function _after() {
		$this->dao = null;
	}

	/**
	 * Tests DAO::getManyToOne()
	 */
	public function testGetManyToOne() {
		$user = $this->dao->getOne ( User::class, "email='benjamin.sherman@gmail.com'", false, null, true );
		$orga = DAO::getManyToOne ( $user, 'organization', false, true );
		$this->assertInstanceOf ( Organization::class, $orga );
	}

	/**
	 * Tests DAO::getOneToMany()
	 */
	public function testGetOneToMany() {
		$orga = DAO::getOne ( Organization::class, 'domain="lecnam.net"', false, null, false );
		$this->assertEquals ( "Conservatoire National des Arts et Métiers", $orga->getName () );
		$this->assertEquals ( 1, $orga->getId () );
		$users = DAO::getOneToMany ( $orga, 'users', true, true );
		$this->assertTrue ( is_array ( $users ) );

		$this->assertTrue ( sizeof ( $users ) > 0 );
		$user = current ( $users );
		$this->assertInstanceOf ( User::class, $user );
	}

	/**
	 * Tests DAO::getManyToMany()
	 */
	public function testGetManyToMany() {
		$user = $this->dao->getOne ( User::class, "email='benjamin.sherman@gmail.com'", false, null, true );
		$groupes = DAO::getManyToMany ( $user, 'groupes', false, null, true );
		$this->assertTrue ( is_array ( $groupes ) );
		$this->assertTrue ( sizeof ( $groupes ) > 0 );
		$groupe = current ( $groupes );
		$this->assertInstanceOf ( Groupe::class, $groupe );
	}

	/**
	 * Tests DAO::affectsManyToManys()
	 */
	public function testAffectsManyToManys() {
		// TODO Auto-generated DAOTest::testAffectsManyToManys()
		$this->markTestIncomplete ( "affectsManyToManys test not implemented" );

		DAO::affectsManyToManys(/* parameters */);
	}

	/**
	 * Tests DAO::getAll()
	 */
	public function testGetAll() {
		$users = $this->dao->getAll ( User::class, '', true, null, true );
		$this->assertEquals ( 101, sizeof ( $users ) );
		$user = current ( $users );
		$this->assertInstanceOf ( User::class, $user );
		$orga = $user->getOrganization ();
		$this->assertInstanceOf ( Organization::class, $orga );
	}

	/**
	 * Tests DAO::getRownum()
	 */
	public function testGetRownum() {
		$users = $this->dao->getAll ( User::class, '', false, null, true );
		$users = array_values ( $users );
		$index = rand ( 0, sizeof ( $users ) - 1 );
		$this->assertEquals ( $index, $this->dao->getRownum ( User::class, $users [$index]->getId () ) );
	}

	/**
	 * Tests DAO::count()
	 */
	public function testCount() {
		$this->assertEquals ( 101, $this->dao->count ( User::class ) );
	}

	/**
	 * Tests DAO::startDatabase()
	 */
	public function testStartDatabase() {
		DAO::startDatabase ( $this->config );
		$this->assertTrue ( DAO::isConnected () );
		$this->assertInstanceOf ( Database::class, DAO::$db );
		$this->assertInstanceOf ( PDO::class, DAO::$db->getPdoObject () );
	}

	/**
	 * Tests DAO::getOne()
	 */
	public function testGetOne() {
		$user = $this->dao->getOne ( User::class, 'firstname="Benjamin"', true, null, true );
		$this->assertInstanceOf ( User::class, $user );
	}

	/**
	 * Tests DAO::uCount()
	 */
	public function testUCount() {
		$res = DAO::uCount ( User::class, "firstname like ? or lastname like ?", [ "b%","a%" ] );
		$this->assertEquals ( 8, $res );
	}

	/**
	 * Tests DAO::uGetAll()
	 */
	public function testuGetAll() {
		$res = DAO::uGetAll ( User::class, "firstname like ? or lastname like ?", false, [ "b%","a%" ] );
		$this->assertEquals ( 8, sizeof ( $res ) );
		$this->assertEquals ( "benjamin.sherman@gmail.com", current ( $res ) . "" );
	}

	/**
	 * Tests DAO::UGetAllWithQuery()
	 */
	public function testUGetAllWithQuery() {
		$users = DAO::uGetAll ( User::class, "groupes.name = ?", [ "groupes" ], [ "Etudiants" ] );
		$this->assertEquals ( "jeremy.bryan", current ( $users ) . "" );
		$this->assertEquals ( 8, sizeof ( $users ) . "" );
	}

	/**
	 * Tests DAO::isConnected()
	 */
	public function testIsConnected() {
		$this->assertTrue ( $this->dao->isConnected () );
	}

	/**
	 * Tests DAO::insert()
	 */
	public function testInsert() {
		$count = DAO::count ( Organization::class );
		$orga = new Organization ();
		$orga->setName ( "orga test" );
		$orga->setDomain ( "dom.com" );
		$orga->setAliases ( "orga alias" );
		$this->dao->insert ( $orga );
		$this->assertEquals ( $count + 1, DAO::count ( Organization::class ) );
		DAO::remove ( $orga );
		$this->assertEquals ( $count, DAO::count ( Organization::class ) );
	}
}

