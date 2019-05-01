<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\[UNAME] as Module;

class [UNAME] extends TestCase
{

	public function getToken($access_token = false)
    {
        $body = [
            'client_id' => 1,
            'client_secret' => '0dH7UpeOXhwmgGwbQYUsSUtgZzGAmctiD7v4xsgR',
            'grant_type' => 'client_credentials',
            'scope' => '*'
        ];

        $response = $this->post('/oauth/token',$body,['Accept' => 'application/json']); 
        if($access_token) {
            return json_decode($response->getContent())->access_token;
        }
        return $response;
    }
    
    /**
     * @group [MODULE]-list
     */
    public function testIndex(){
        
        $response = $this->get('api/[MODULE]s',
                        [
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer ".$this->getToken(true)
                        ]
                    );
        $response->assertSeeText(200);
    }

    /**
     * @group [MODULE]-store
     */
    public function testStore(){
        $body =  [        
                    [TESTCASEDATA]        
                ];

        $response = $this->post('/api/[MODULE]/store',$body,
                        [
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer ".$this->getToken(true)
                        ]
                    );
        $response->assertSeeText('[ULABEL] Created!');
    }

    /**
     * @group [MODULE]-edit
     */
    public function testEdit(){

        $model = Module::orderBy('id', 'desc')->first();

        $response = $this->get('api/[MODULE]/edit/'.$model->id,
                        [
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer ".$this->getToken(true)
                        ]
                    );
        $response->assertSeeText(200);
    }

    /**
     * @group [MODULE]-update
     */
    public function testUpdate(){

        $model = Module::orderBy('id', 'desc')->first();

        $body =  [        
                    [TESTCASEDATA]
                    'id' => $model->id,        
                ];

        $response = $this->post('/api/[MODULE]/update',$body,
                        [
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer ".$this->getToken(true)
                        ]
                    );
        $response->assertSeeText('[ULABEL] Updated!');
    }

    /**
     * @group [MODULE]-delete
     */
    public function testDelete(){

        $model = Module::orderBy('id', 'desc')->first();

        $response = $this->get('api/[MODULE]/destroy/'.$model->id,
                        [
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer ".$this->getToken(true)
                        ]
                    );
        $response->assertSeeText('[ULABEL] Deleted!');
    }
}
