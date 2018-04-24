<?php

namespace MelTests\Unit\Resources\Questions;

use Mel\Resources\Questions\Question;
use MelTests\TestCase;

class QuestionTest extends TestCase
{
    protected $jsonAnsweredQuestion;

    protected $jsonUnansweredQuestion;

    protected function setUp()
    {
        parent::setUp();
        $this->jsonAnsweredQuestion = $this->getJsonFileContent('questions/single-question-answered');
        $this->jsonUnansweredQuestion = $this->getJsonFileContent('questions/single-question-unanswered');
    }

    public function testShouldFillObjectCorrectly()
    {
        $questionAttributes = json_decode($this->jsonAnsweredQuestion, true);

        $question = new Question($this->getMel());

        $question->fill($questionAttributes);

        $this->assertEquals($questionAttributes['id'], $question->id);
        $this->assertInstanceOf(\stdClass::class, $question->answer);
        $this->assertInstanceOf(\stdClass::class, $question->from);
        $this->assertEquals($questionAttributes['from']['id'], $question->from->id);
    }


    public function testShouldFindQuestionUsingItemId()
    {
        $searchData = json_decode($this->getJsonFileContent('questions/search-questions-response'), true);

        $mel = $this->getMel($this->mockClient);

        $this->mockClient->setDefaultResponse(
            $this->createResponse(
                $this->getJsonFileContent('questions/search-questions-response')
            )
        );

        $question = new Question($mel);

        $searchResult = $question->searchByItem('MLA608007087');

        $request = $this->mockClient->getLastRequest();

        $this->assertEquals($searchData, $searchResult->getAttributes());

        $this->assertEquals(
            $this->apiUri . 'questions/search?item=MLA608007087',
            $request->getUri()->__toString()
        );
    }

    public function testShouldGetQuestionById()
    {
        $questionData = json_decode($this->jsonAnsweredQuestion, true);

        $mel = $this->getMel($this->mockClient);

        $this->mockClient->setDefaultResponse(
            $this->createResponse(
                $this->jsonAnsweredQuestion
            )
        );

        $question = new Question($mel);

        $questionResult = $question->getById('3957150025');

        $request = $this->mockClient->getLastRequest();

        $this->assertNotSame($question, $questionResult);
        $this->assertEquals($questionData, $questionResult->getAttributes());

        $this->assertEquals(
            $this->apiUri . 'questions/3957150025',
            $request->getUri()->__toString()
        );
    }

    public function testShouldCreateQuestion()
    {
        $arrayUnanswered = json_decode($this->jsonUnansweredQuestion, true);


        $this->mockClient->setDefaultResponse(
            $this->createResponse($this->jsonUnansweredQuestion)
        );

        $question = new Question($this->getMel($this->mockClient));

        $questionCreated = $question->create(['item_id' => 'MLA608007087', 'text' => 'Test question.']);

        $request = $this->mockClient->getLastRequest();

        $this->assertNotSame($question, $questionCreated);
        $this->assertEquals($arrayUnanswered, $questionCreated->getAttributes());

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testShouldAnswerQuestion()
    {
        $arrayAnswered = json_decode($this->jsonAnsweredQuestion, true);


        $this->mockClient->setDefaultResponse(
            $this->createResponse($this->jsonAnsweredQuestion)
        );

        $question = new Question($this->getMel($this->mockClient));

        $questionCreated = $question->answer(['question_id' => '3957150025', 'text' => 'Test answer...']);

        $request = $this->mockClient->getLastRequest();

        $this->assertNotSame($question, $questionCreated);
        $this->assertEquals($arrayAnswered, $questionCreated->getAttributes());

        $this->assertEquals('POST', $request->getMethod());
    }
}
