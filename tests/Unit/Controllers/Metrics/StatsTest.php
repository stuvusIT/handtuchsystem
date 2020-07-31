<?php

namespace Handtuchsystem\Test\Unit\Controllers\Metrics;

use Carbon\Carbon;
use Handtuchsystem\Controllers\Metrics\Stats;
use Handtuchsystem\Models\LogEntry;
use Handtuchsystem\Models\Message;
use Handtuchsystem\Models\News;
use Handtuchsystem\Models\Question;
use Handtuchsystem\Models\User\PasswordReset;
use Handtuchsystem\Models\User\PersonalData;
use Handtuchsystem\Models\User\Settings;
use Handtuchsystem\Models\User\State;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;
use Illuminate\Support\Str;
use Psr\Log\LogLevel;

class StatsTest extends TestCase
{
    use HasDatabase;

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::__construct
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::newUsers
     */
    public function testNewUsers()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $this->assertEquals(2, $stats->newUsers());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::vouchers
     */
    public function testVouchers()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $this->assertEquals(14, $stats->vouchers());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::tshirts
     */
    public function testTshirts()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $this->assertEquals(2, $stats->tshirts());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::tshirtSizes
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::raw
     */
    public function testTshirtSizes()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $sizes = $stats->tshirtSizes();
        $this->assertCount(2, $sizes);
        $this->assertEquals([
            ['shirt_size' => 'L', 'count' => 2],
            ['shirt_size' => 'XXL', 'count' => 1],
        ], $sizes->toArray());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::languages
     */
    public function testLanguages()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $languages = $stats->languages();
        $this->assertCount(2, $languages);
        $this->assertEquals([
            ['language' => 'lo_RM', 'count' => 2],
            ['language' => 'te_ST', 'count' => 7],
        ], $languages->toArray());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::themes
     */
    public function testThemes()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $themes = $stats->themes();
        $this->assertCount(3, $themes);
        $this->assertEquals([
            ['theme' => 0, 'count' => 7],
            ['theme' => 1, 'count' => 1],
            ['theme' => 4, 'count' => 1],
        ], $themes->toArray());
    }


    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::announcements
     */
    public function testAnnouncements()
    {
        $this->addUsers();
        $newsData = ['title' => 'Test', 'text' => 'Foo Bar', 'user_id' => 1];

        (new News($newsData))->save();
        (new News($newsData))->save();
        (new News($newsData + ['is_meeting' => true]))->save();

        $stats = new Stats($this->database);
        $this->assertEquals(3, $stats->announcements());
        $this->assertEquals(2, $stats->announcements(false));
        $this->assertEquals(1, $stats->announcements(true));
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::questions
     */
    public function testQuestions()
    {
        $this->addUsers();
        $questionsData = ['text' => 'Lorem Ipsum', 'user_id' => 1];

        (new Question($questionsData))->save();
        (new Question($questionsData))->save();
        (new Question($questionsData + ['answerer_id' => 2, 'answer' => 'Dolor sit!']))->save();

        $stats = new Stats($this->database);
        $this->assertEquals(3, $stats->questions());
        $this->assertEquals(2, $stats->questions(false));
        $this->assertEquals(1, $stats->questions(true));
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::arrivedUsers
     */
    public function testArrivedUsers()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $this->assertEquals(7, $stats->arrivedUsers());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::forceActiveUsers
     */
    public function testForceActiveUsers()
    {
        $this->addUsers();

        $stats = new Stats($this->database);
        $this->assertEquals(2, $stats->forceActiveUsers());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::messages
     */
    public function testMessages()
    {
        $this->addUsers();

        (new Message(['user_id' => 1, 'receiver_id' => 2, 'text' => 'Ohi?']))->save();
        (new Message(['user_id' => 4, 'receiver_id' => 1, 'text' => 'Testing stuff?']))->save();
        (new Message(['user_id' => 2, 'receiver_id' => 3, 'text' => 'Nope!', 'read' => true]))->save();

        $stats = new Stats($this->database);
        $this->assertEquals(3, $stats->messages());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::sessions
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::getQuery
     */
    public function testSessions()
    {
        $this->database
            ->getConnection()
            ->table('sessions')
            ->insert([
                ['id' => 'asd', 'payload' => 'data', 'last_activity' => new Carbon('1 month ago')],
                ['id' => 'efg', 'payload' => 'lorem', 'last_activity' => new Carbon('55 minutes ago')],
                ['id' => 'hij', 'payload' => 'ipsum', 'last_activity' => new Carbon('3 seconds ago')],
                ['id' => 'klm', 'payload' => 'dolor', 'last_activity' => new Carbon()],
            ]);

        $stats = new Stats($this->database);
        $this->assertEquals(4, $stats->sessions());
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::databaseRead
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::databaseWrite
     */
    public function testDatabase()
    {
        $stats = new Stats($this->database);

        $read = $stats->databaseRead();
        $write = $stats->databaseWrite();

        $this->assertIsFloat($read);
        $this->assertNotEmpty($read);
        $this->assertIsFloat($write);
        $this->assertNotEmpty($write);
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::logEntries
     */
    public function testLogEntries()
    {
        (new LogEntry(['level' => LogLevel::INFO, 'message' => 'Some info']))->save();
        (new LogEntry(['level' => LogLevel::INFO, 'message' => 'Another info']))->save();
        (new LogEntry(['level' => LogLevel::CRITICAL, 'message' => 'A critical error!']))->save();
        (new LogEntry(['level' => LogLevel::DEBUG, 'message' => 'Verbose output!']))->save();
        (new LogEntry(['level' => LogLevel::INFO, 'message' => 'Shutdown initiated']))->save();
        (new LogEntry(['level' => LogLevel::WARNING, 'message' => 'Please be cautious']))->save();

        $stats = new Stats($this->database);
        $this->assertEquals(6, $stats->logEntries());
        $this->assertEquals(3, $stats->logEntries(LogLevel::INFO));
        $this->assertEquals(1, $stats->logEntries(LogLevel::DEBUG));
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Stats::passwordResets
     */
    public function testPasswordResets()
    {
        $this->addUsers();

        (new PasswordReset(['user_id' => 1, 'token' => 'loremIpsum123']))->save();
        (new PasswordReset(['user_id' => 3, 'token' => '5omeR4nd0mTok3N']))->save();

        $stats = new Stats($this->database);
        $this->assertEquals(2, $stats->passwordResets());
    }

    /**
     * Add some example users
     */
    protected function addUsers()
    {
        $this->addUser();
        $this->addUser([], ['shirt_size' => 'L']);
        $this->addUser(['arrived' => 1]);
        $this->addUser(['arrived' => 1], [], ['language' => 'lo_RM']);
        $this->addUser(['arrived' => 1, 'got_voucher' => 2], ['shirt_size' => 'XXL'], ['language' => 'lo_RM']);
        $this->addUser(['arrived' => 1, 'got_voucher' => 9, 'force_active' => true], [], ['theme' => 1]);
        $this->addUser(['arrived' => 1, 'got_voucher' => 3], ['theme' => 10]);
        $this->addUser(['arrived' => 1, 'active' => 1, 'got_shirt' => true, 'force_active' => true]);
        $this->addUser(['arrived' => 1, 'active' => 1, 'got_shirt' => true], ['shirt_size' => 'L'], ['theme' => 4]);
    }

    /**
     * @param array $state
     * @param array $personalData
     * @param array $settings
     */
    protected function addUser(array $state = [], $personalData = [], $settings = [])
    {
        $name = 'user_' . Str::random(5);

        $user = new User([
            'name'     => $name,
            'password' => '',
            'email'    => $name . '@engel.example.com',
            'api_key'  => '',
        ]);
        $user->save();

        $state = new State($state);
        $state->user()
            ->associate($user)
            ->save();

        $personalData = new PersonalData($personalData);
        $personalData->user()
            ->associate($user)
            ->save();

        $settings = new Settings(array_merge([
            'language'        => 'te_ST',
            'theme'           => 0,
            'email_human'     => '',
            'email_shiftinfo' => '',
        ], $settings));
        $settings->user()
            ->associate($user)
            ->save();
    }

    /**
     * Set up the environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
    }
}
