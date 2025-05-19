<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            'VenuesTableSeeder',
            'PromotersTableSeeder',
            'OtherServicesTableSeeder',
            'OtherServicesListTableSeeder'
        ]);
    }

    /**
     * Testing guests can access homepage, about, credits, contact, gig guide, public events
     */
    public function test_guest_can_access_homepage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertSee('Your Next Show');
    }

    public function test_guest_can_access_about(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertViewIs('about');
        $response->assertSee('What is it?');
    }

    public function test_guest_can_access_credits(): void
    {
        $response = $this->get('/credits');

        $response->assertStatus(200);
        $response->assertViewIs('credits');
        $response->assertSee('Credits');
    }

    public function test_guest_can_access_contact(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertViewIs('contact');
        $response->assertSee('Contact');
    }

    public function test_guest_can_access_gig_guide(): void
    {
        $response = $this->get('/gig-guide');

        $response->assertStatus(200);
        $response->assertViewIs('gig-guide');
        $response->assertSee('Gig Guide');
    }

    public function test_guest_can_access_events(): void
    {
        $response = $this->get('/events');

        $response->assertStatus(200);
        $response->assertViewIs('events');
        $response->assertSee('Upcoming Events');
    }

    public function test_guest_can_access_privacy_policy(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertViewIs('privacy-policy');
    }

    /**
     * Testing guests can access venue listing and profile page
     */
    public function test_guest_can_access_venues_listing()
    {
        $response = $this->get('/venues');

        $response->assertStatus(200);
        $response->assertViewIs('venues');
        $response->assertSee('Find Your Next');
    }

    public function test_guest_can_access_single_venue_page()
    {
        $response = $this->get('/venues/the-forum-music-studios');

        $response->assertStatus(200);
        $response->assertViewIs('venue');
        $response->assertSee('The Forum Music Studios');
    }

    /**
     * Testing guests can access promoters listing and profile page
     */
    public function test_guest_can_access_promoters_listing()
    {
        $response = $this->get('/promoters');

        $response->assertStatus(200);
        $response->assertViewIs('promoters');
        $response->assertSee('Find Your Next');
    }

    public function test_guest_can_access_single_promoter_page()
    {
        $response = $this->get('/promoters/krn-promotions');

        $response->assertStatus(200);
        $response->assertViewIs('promoter');
        $response->assertSee('KRN Promotions');
    }

    /**
     * Testing guests can access other services group page
     */
    public function test_guest_can_access_other_services_group()
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertViewIs('other');
        $response->assertSee('Find Your Next');
    }

    public function test_guest_can_access_photography_listing()
    {
        $response = $this->get('/services/photography');

        $response->assertStatus(200);
        $response->assertViewIs('single-service-group');
        $response->assertSee('Photography');
    }

    public function test_guest_can_access_photographer_page()
    {
        $response = $this->get('/services/photography/ollie-hayman-photography');

        $response->assertStatus(200);
        $response->assertViewIs('single-service');
        $response->assertSee('Ollie Hayman Photography');
    }

    public function test_guest_can_access_videographer_listing()
    {
        $response = $this->get('/services/videography');

        $response->assertStatus(200);
        $response->assertViewIs('single-service-group');
        $response->assertSee('Videography');
    }

    public function test_guest_can_access_videographer_page()
    {
        $response = $this->get('/services/videography/robcamproductions');

        $response->assertStatus(200);
        $response->assertViewIs('single-service');
        $response->assertSee('RobCamProductions');
    }

    public function test_guest_can_access_designer_listing()
    {
        $response = $this->get('/services/designer');

        $response->assertStatus(200);
        $response->assertViewIs('single-service-group');
        $response->assertSee('Designer');
    }

    public function test_guest_can_access_designer_page()
    {
        $response = $this->get('/services/designer/dilluzional-illustration');

        $response->assertStatus(200);
        $response->assertViewIs('single-service');
        $response->assertSee('Dilluzional Illustration');
    }

    public function test_guest_can_access_artist_listing()
    {
        $response = $this->get('/services/artist');

        $response->assertStatus(200);
        $response->assertViewIs('single-service-group');
        $response->assertSee('Artist');
    }

    public function test_guest_can_access_artist_page()
    {
        $response = $this->get('/services/artist/anafae-music');

        $response->assertStatus(200);
        $response->assertViewIs('single-service');
        $response->assertSee('Anafae Music');
    }

    /**
     * Testing guests can access register and login pages
     */

    public function test_guests_can_access_register_page()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register');
    }

    public function test_guests_can_access_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Login');
    }
}