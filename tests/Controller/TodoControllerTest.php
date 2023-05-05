<?php

namespace App\Tests\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TodoControllerTest extends WebTestCase
{
    private TodoRepository $todoRepository;
    private UserRepository $userRepository;
    private KernelBrowser $client;

    /**
     * Initializing attributes
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = $this->client->getKernel();

        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->todoRepository = $entityManager->getRepository(Todo::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * Test the format of a paginated response
     */
    private function testPaginatedResponseFormat(): void
    {
        // Retrieve the result of the response
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        // Check the presence and the type of the "data" field
        $this->assertArrayHasKey("data", $result);
        $this->assertIsArray($result["data"]);

        // Check the format of each element within the "data" field
        foreach ($result["data"] as $todo) {
            $this->testTodoFormat($todo);
        }

        // Perform the same operations for the "pagination" field
        $this->assertArrayHasKey("pagination", $result);
        $this->assertIsArray($result["pagination"]);

        $paginationKeys = ["total", "count", "offset", "items_per_page", "total_pages", "current_page", "has_next_page", "has_previous_page", ];
        foreach ($paginationKeys as $key) {
            $this->assertArrayHasKey($key, $result["pagination"]);
        }
    }

    /**
     * Test the format of a todo element
     */
    private function testTodoFormat(array $todoAsArray): void
    {
        // Check the presence of each todo fields
        $todoKeys = ["id", "title", "createdAt", "updatedAt", "completed"];
        foreach ($todoKeys as $key) {
            $this->assertArrayHasKey($key, $todoAsArray);
        }
    }

    /**
     * Test the GET /api/todos route
     */
    public function testGetTodos(): void
    {
        // Make a request with default page parameter
        $this->client->request('GET', '/api/todos');

        // Check if the request is valid
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $this->testPaginatedResponseFormat();

        // Perform the same operations with a custom page parameter
        $this->client->request('GET', '/api/todos?page=2');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $this->testPaginatedResponseFormat();
    }

    /**
     * Test the GET /api/todos/{id} route
     */
    public function testGetTodo(): void
    {
        // Retrieve a todo from the database
        $todo = $this->todoRepository->findOneBy([]);

        // Make the request
        $this->client->request('GET', "/api/todos/{$todo->getId()}");

        // Check if it's successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testTodoFormat($result);
    }

    /**
     * Test the POST /api/todo route
     */
    public function testCreateTodo(): void
    {
        // Make the request with body paramater without the "X-AUTH-TOKEN" header to chech the security
        $this->client->request('POST', "/api/todos", content: json_encode(["title" => "new Todo"]));

        // Check if the response status code is "401 Unauthorized"
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Retrieve a user from the database
        $user = $this->userRepository->findOneBy([]);

        // Make the request with the token header and the same body parameter
        $this->client->request(
            'POST',
            "/api/todos",
            server: [
                "HTTP_X_AUTH_TOKEN" => $user->getToken()
            ],
            content: json_encode(["title" => "new Todo"])
        );

        // Check if the response if successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testTodoFormat($result);

        $this->assertSame("new Todo", $result["title"]);
    }

    /**
     * Test the DELETE /api/todos/{id} route
     */
    public function testDeleteTodo(): void
    {
        // As for the previous method, we first make the request without the token header
        $todo = $this->todoRepository->findOneBy([]);
        $this->client->request('DELETE', "/api/todos/{$todo->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Make the request with the token header
        $user = $this->userRepository->findOneBy([]);
        $this->client->request(
            'DELETE',
            "/api/todos/{$todo->getId()}",
            server: [
                "HTTP_X_AUTH_TOKEN" => $user->getToken()
            ],
        );

        // Check if the request is successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test the PATCH /api/todos/{id} route
     */
    public function testPartialUpdate(): void
    {
        $todo = $this->todoRepository->findOneBy([]);
        $this->client->request('PATCH', "/api/todos/{$todo->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $user = $this->userRepository->findOneBy([]);
        $this->client->request(
            'PATCH',
            "/api/todos/{$todo->getId()}",
            server: [
                "HTTP_X_AUTH_TOKEN" => $user->getToken()
            ],
            content: json_encode(["title" => "Updated title"])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testTodoFormat($result);

        $this->assertSame("Updated title", $result["title"]);
    }

    /**
     * Test the PUT /api/todos/{id} route
     */
    public function testFullUpdate(): void
    {
        $todo = $this->todoRepository->findOneBy([]);
        $this->client->request('PUT', "/api/todos/{$todo->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $user = $this->userRepository->findOneBy([]);

        // Missing parameter
        $this->client->request(
            'PUT',
            "/api/todos/{$todo->getId()}",
            server: [
                "HTTP_X_AUTH_TOKEN" => $user->getToken()
            ],
            content: json_encode(["title" => "Updated title"])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Valid request
        $this->client->request(
            'PUT',
            "/api/todos/{$todo->getId()}",
            server: [
                "HTTP_X_AUTH_TOKEN" => $user->getToken()
            ],
            content: json_encode(["title" => "Updated title", "completed" => true])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testTodoFormat($result);

        $this->assertSame("Updated title", $result["title"]);
        $this->assertSame(true, $result["completed"]);
    }
}
