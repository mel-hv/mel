<?php

namespace MelTests\Unit\Resources\Questions;

use Mel\Resources\Questions\Question;
use MelTests\TestCase;

class QuestionTest extends TestCase
{
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

        $this->assertEquals($searchData['total'], $searchResult->total);

        $this->assertEquals(
            $this->apiUri.'questions/search?item=MLA608007087',
            $request->getUri()->__toString()
        );
    }
    
//    public function testShouldGetQuestionById()
//    {
//        $questionData = json_decode($this->getJsonFileContent('questions/single-question'), true);
//
//        $mel = $this->getMel($this->mockClient);
//
//        $this->mockClient->setDefaultResponse(
//            $this->createResponse(
//                $this->getJsonFileContent('questions/single-question')
//            )
//        );
//
//        $question = new Question($mel);
//
//        $questionResult = $question->getById('3957150025');
//
//        $request = $this->mockClient->getLastRequest();
//
//        $this->assertNotSame($question, $questionResult);
//        $this->assertEquals($questionData['id'], $questionResult->id);
//        $this->assertEquals($questionData['text'], $questionResult->text);
//
//        $this->assertEquals(
//            $this->apiUri . 'questions/3957150025',
//            $request->getUri()->__toString()
//        );
//    }
}
