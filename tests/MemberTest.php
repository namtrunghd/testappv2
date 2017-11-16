<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use App\Member;
use Faker\Factory;

class MemberTest extends TestCase
{
	use DatabaseMigrations;
	use WithoutMiddleware;


	public function testListMember()
	{
		$response = $this->get('list');
		$this->assertResponseStatus(200);
	}


	public function testAddMember()
	{
		$member = [
			'name' => 'Tran Hong Nhung',
			'address' => 'aa',
			'age' => '11'
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	public function testAddMemberHasAddressScript()
	{
		$photo = new UploadedFile(base_path('public\photo\01.jpg'),
			'01.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = [
			'name' => 'Nhung',
			'address' => '<script>alert("Boom Boom");</script>',
			'age' => 33,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	
	public function testAddMemberHasPhotoJPG()
	{
		$photo = new UploadedFile(base_path('public\photo\01.jpg'),
			'01.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = [
			'name' => 'Nhung',
			'address' => 'Ha Tay',
			'age' => 33,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}
	public function testAddMemberHasPhotoPNG()
	{
		$photo = new UploadedFile(base_path('public\photo\010.png'),
			'010.png', 'image/png', 111, $error = null, $test = true);
		$member = [
			'name' => 'Nhung',
			'address' => 'Ha Tay',
			'age' => 33,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	public function testAddMemberHasPhotoGIF()
	{
		$photo = new UploadedFile(base_path('public\photo\011.gif'),
			'011.gif', 'image/gif', 111, $error = null, $test = true);
		$member = [
			'name' => 'Nhung',
			'address' => 'Ha Tay',
			'age' => 33,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	public function testAddMemberNullName(){
		
		$member = [
			'name' => '',
			'address' => 'Maxtcova',
			'age' => 69
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Name field is required.',$success);
		}
		$this->assertSame("The Name field is required.",$response->exception->validator->messages()->first());
	}

	public function testAddMemberNullAddress()
	{
		$member = [
			'name' => 'Hong Nhung',
			'address' => '',
			'age' => 23,
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Address field is required.',$success);
		}
		$this->assertSame("The Address field is required.",$response->exception->validator->messages()->first());
	}

	public function testAddMemberNullAge()
	{
		$member = [
			'name' => 'Hong Nhung',
			'address' => 'Truong Son Tay',
			'age' => '',
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Age field is required.',$success);
		}
		$this->assertSame("The Age field is required.",$response->exception->validator->messages()->first());
	}

	public function testAddNameAlphabetic()
	{   
		$member = [
			'name' => 'Member0001',
			'address' => 'Hong Kong',
			'age' => 99
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('Name can contain only Alphabetic characters.',$success);
		}
		$this->assertSame("Name can contain only Alphabetic characters.",$response->exception->validator->messages()->first());
	}

	public function testAddAgeNotNumeric()
	{   
		$member = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 'date'
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Age must be a number.',$success);
		}
		$this->assertSame("The Age must be a number.",$response->exception->validator->messages()->first());
	}

	public function testAddAgeMax2Characters()
	{   
		$member = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 223
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals("Maximum 2 characters",$success);
		}
		$this->assertSame("Maximum 2 characters",$response->exception->validator->messages()->first());
	}

	public function testAddName100Characters()
	{
		$member = [
			'name' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
			'address' => 'Hang Gai',
			'age' => 55,
		];
		$response = $this->call('POST', 'add', $member);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	public function testAddNameGreaterThan100Characters()
	{
		$member = [
			'name' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopQ',
			'address' => 'Hai Phong',
			'age' => 55,
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The name may not be greater than 100 characters.',$success);
		}
		$this->assertSame("The name may not be greater than 100 characters.",$response->exception->validator->messages()->first());
	}

	public function testAddAddress300Characters()
	{
		$member = [
			'name' => 'Hoang Anh',
			'address' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
			'age' => 55,
		];
		$response = $this->call('POST', 'add', $member);
        // dd($response);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Add Success',$result);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $member['name'],
			'address' => $member['address'],
			'age' => $member['age'],
		]);
	}

	public function testAddAddressGreaterThan300Characters()
	{
		$member = [
			'name' => 'What Your Name',
			'address' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopE',
			'age' => 66,
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The name may not be greater than 300 characters.',$success);
		}
		$this->assertSame("The name may not be greater than 300 characters.",$response->exception->validator->messages()->first());
	}


	public function testAddImageGreaterThan10Mb()
	{
		$photo = new UploadedFile(base_path('public\photo\img10MB.jpg'),
			'img10MB.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = [
			'name' => 'Tran Hong Nhung ',
			'address' => 'Ha Nam, Ha Bac',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$error = $response->getContent();
			$this->assertEquals('The photo may not be greater than 10MB.',$error);
		}
		$this->assertSame("The photo may not be greater than 10MB.",$response->exception->validator->messages()->first());
	}

	public function testAddImageNotValidExtension()
	{
		$photo = new UploadedFile(base_path('public\photo\image.bmp'),
			'image.bmp', 'image/jpg', 111, $error = null, $test = true);
		$member = [
			'name' => 'Tran Hoang ',
			'address' => 'Ha Nam',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$result = $response->getContent();
			$this->assertEquals('Photo only allow JPG, GIF, and PNG filetypes.',$result);
		}
		$this->assertSame("Photo only allow JPG, GIF, and PNG filetypes.",$response->exception->validator->messages()->first());
	}

	public function testAddImageNotValidImage()
	{
		$photo = new UploadedFile(base_path('public\photo\not_img.png'),
			'not_img.png', 'image/png', 111, $error = null, $test = true);
		$member = [
			'name' => 'Ngoc Anh',
			'address' => 'Effel',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST', 'add', $member);
		if (!$response->exception) {
			$result = $response->getContent();
			$this->assertEquals('Uploaded file is not a valid image',$result);
		}
		$this->assertSame("Uploaded file is not a valid image",$response->exception->validator->messages()->first());
	}

	public function testDeleteMember()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Administrator',
			'address' => 'Land-Mark',
			'age' => 21
		]);
		$response = $this->call('GET', 'delete/'.$member->id);
		$this->assertResponseStatus(200);
		$this->notSeeInDatabase('members',
			[
				'name' => 'Administrator',
				'address' => 'Land-Mark',
				'age' => 21
			]);
	}

	public function testDeleteMemberHasPhoto()
	{
		$photo = new UploadedFile(base_path('public\photo\01.jpg'),
			'01.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Administrator',
			'address' => 'Land-Mark',
			'age' => 21,
			'photo'=>$photo
		]);
		$response = $this->call('GET', 'delete/'.$member->id);
		$this->assertResponseStatus(200);
		$this->notSeeInDatabase('members',
			[
				'name' => 'Administrator',
				'address' => 'Land-Mark',
				'age' => 21
			]);
	}


	public function testEditMember()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Nam Trung',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members',
			[
				'name' => $update['name'],
				'address' => $update['address'],
				'age' => $update['age']
			]);
	}

	public function testEditMemberHasPhoto()
	{
		$photo = new UploadedFile(base_path('public\photo\01.jpg'),
			'01.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Administrator',
			'address' => 'Land-Mark',
			'age' => 21,
			'photo'=>$photo
		]);
		$update = [
			'name' => 'Nam Trung',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if ($response->exception) {
			$res = $response->exception->validator->messages()->first();
			$this->assertEquals('Edit Success',$res);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members',
			[
				'name' => $update['name'],
				'address' => $update['address'],
				'age' => $update['age']
			]);
	}

	public function testEditMemberNameNull()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => '',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Name field is required.',$success);
		}
		$this->assertSame("The Name field is required.",$response->exception->validator->messages()->first());
	}

	public function testEditMemberNameAlphabetic()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Nam Trung 01',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('Name can contain only Alphabetic characters.',$success);
		}
		$this->assertSame("Name can contain only Alphabetic characters.",$response->exception->validator->messages()->first());
	}

	public function testEditMemberName100Characters()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Edit Success',$result);
		}   
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members',
			[
				'name' => $update['name'],
				'address' => $update['address'],
				'age' => $update['age'],
			]);
	}

	public function testEditMemberNameGreaterThan100Characters()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopA',
			'address' => 'Sai Gon',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The name may not be greater than 100 characters.',$success);
		}
		$this->assertSame("The name may not be greater than 100 characters.",$response->exception->validator->messages()->first());
	}

	public function testEditMemberAddressNull()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Nam Trung',
			'address' => '',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Address field is required.',$success);
		}
		$this->assertSame("The Address field is required.",$response->exception->validator->messages()->first());
	}

	public function testEditMemberAddress300Characters()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Hong Anh',
			'address' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if ($response->exception) {
			$result = $response->exception->validator->messages()->first();
			$this->assertEquals('Edit Success',$result);
		}   
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members',
			[
				'name' => $update['name'],
				'address' => $update['address'],
				'age' => $update['age'],
			]);
	}

	public function testEditMemberAddressGreaterThan300Characters()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Hong Anh',
			'address' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopAA',
			'age' => 21
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The name may not be greater than 300 characters.',$success);
		}
		$this->assertSame("The name may not be greater than 300 characters.",$response->exception->validator->messages()->first());
	}

	public function testEditMemberAgesNull()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Hong Nhung',
			'address' => 'London',
			'age' => 23
		]);
		$update = [
			'name' => 'Nam Trung',
			'address' => 'Ha Noi',
			'age' => ''
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Age field is required.',$success);
		}
		$this->assertSame("The Age field is required.",$response->exception->validator->messages()->first());
	}

	public function testEditAgeNotNumeric()
	{
		$member = Factory(Member::class)->create([
			'name' => 'Ronaldo',
			'address' => 'Ciao',
			'age' => 23,
		]);
		$update = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 'date'
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('The Age must be a number.',$success);
		}
		$this->assertSame("The Age must be a number.",$response->exception->validator->messages()->first());
	}

	public function testEditAgeMax2Characters()
	{   
		$member = Factory(Member::class)->create([
			'name' => 'Ronaldo',
			'address' => 'Ciao',
			'age' => 23,
		]);
		$update = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 232
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$success = $response->getContent();
			$this->assertEquals('Maximum 2 characters',$success);
		}
		$this->assertSame("Maximum 2 characters",$response->exception->validator->messages()->first());
	}

	public function testEditImageNotValidImage()
	{
		$photo = new UploadedFile(base_path('public\photo\not_img.png'),
			'not_img.png', 'image/png', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Ngoc Anh',
			'address' => 'Effel',
			'age' => 23,
			'photo' => $photo
		]);
		$update = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$result = $response->getContent();
			$this->assertEquals('Uploaded file is not a valid image',$result);
		}
		$this->assertSame("Uploaded file is not a valid image",$response->exception->validator->messages()->first());
	}

	public function testEditImageNotValidExtension()
	{
		$photo = new UploadedFile(base_path('public\photo\image.bmp'),
			'image.bmp', 'image/jpg', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Ngoc Anh',
			'address' => 'Effel',
			'age' => 23,
			'photo' => $photo
		]);
		$update = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$result = $response->getContent();
			$this->assertEquals('Photo only allow JPG, GIF, and PNG filetypes.',$result);
		}
		$this->assertSame("Photo only allow JPG, GIF, and PNG filetypes.",$response->exception->validator->messages()->first());
	}


	public function testEditImageGreaterThan10Mb()
	{
		$photo = new UploadedFile(base_path('public\photo\img10MB.jpg'),
			'img10MB.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Tran Hong Nhung ',
			'address' => 'Ha Nam, Ha Bac',
			'age' => 23,
			'photo' => $photo
		]);
		$update = [
			'name' => 'Member',
			'address' => 'Macao',
			'age' => 23,
			'photo' => $photo
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if (!$response->exception) {
			$error = $response->getContent();
			$this->assertEquals('The photo may not be greater than 10MB.',$error);
		}
		$this->assertSame("The photo may not be greater than 10MB.",$response->exception->validator->messages()->first());
	}
	
	public function testEditMemberHasAddressScript()
	{
		$photo = new UploadedFile(base_path('public\photo\01.jpg'),
			'01.jpg', 'image/jpg', 111, $error = null, $test = true);
		$member = Factory(Member::class)->create([
			'name' => 'Nhung',
			'address' => 'Noi nay co Anh',
			'age' => 33,
			'photo' => $photo
		]);
		$update = [
			'name' => 'Nhung',
			'address' => '<script>alert("Boom Boom");</script>',
			'age' => 33,
			'photo' => $photo
		];
		$response = $this->call('POST','edit/'.$member->id, $update);
		if ($response->exception) {
			$res = $response->exception->validator->messages()->first();
			print_r($res);
		}
		$this->assertResponseStatus(200);
		$this->seeInDatabase('members', [
			'name' => $update['name'],
			'address' => $update['address'],
			'age' => $update['age'],
		]);
	}

}
