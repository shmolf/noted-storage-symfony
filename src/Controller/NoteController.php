<?php

namespace App\Controller;

use App\Entity\MarkdownNote;
use App\Entity\NoteTag;
use App\Entity\User;
use App\Repository\MarkdownNoteRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Ramsey\Uuid\Uuid;
use shmolf\NotedHydrator\JsonSchema\v2\Library;
use shmolf\NotedHydrator\NoteHydrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class NoteController extends AbstractController
{
    public function getNoteList(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $notes = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findBy(['user' => $user], ['lastModified' => 'DESC']);

        $noteList = array_map(function(MarkdownNote $note) {
            $createdDate = new DateTime(
                $note->getCreatedDate()?->format(DateTimeInterface::ISO8601),
                new DateTimeZone('UTC')
            );
            $lastModified = new DateTime(
                $note->getLastModified()?->format(DateTimeInterface::ISO8601),
                new DateTimeZone('UTC')
            );

            return [
                'title' => $note->getTitle(),
                'tags' => array_map(fn(NoteTag $tag) => $tag->getName(), $note->getTags()->toArray()),
                'uuid' => $note->getUuid(),
                'inTrashcan' => $note->getInTrashcan(),
                'createdDate' => $createdDate->format(DateTimeInterface::ISO8601),
                'lastModified' => $lastModified->format(DateTimeInterface::ISO8601),
            ];
        }, $notes);

        return new JsonResponse($noteList);
    }

    public function getNotesForUser(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $notes = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findBy(['user' => $user]);

        $jsonResponse = $this->json($notes, 200, [], [
            'groups' => ['main'],
        ]);

        $now = (new DateTime())->format('Y-m-d-His');
        $disposition = $jsonResponse->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "note'd-export-{$now}.json"
        );

        $jsonResponse->headers->set('Content-Disposition', $disposition);

        return $jsonResponse;
    }

    public function newNote(MarkdownNoteRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        $noteEntity = $repo->new($user);

        return $this->json($noteEntity, 200, [], [
            'groups' => ['main'],
        ]);
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function getNoteByUuid(string $uuid): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        if (Uuid::isValid($uuid) === false) {
            throw new Exception('Invalid UUID provided');
        }

        $note = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findOneBy(['user' => $user, 'uuid' => $uuid]);

        return $this->json($note, 200, [], [
            'groups' => ['main'],
        ]);
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function deleteNoteByUuid(string $uuid, MarkdownNoteRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        if (Uuid::isValid($uuid) === false) {
            throw new Exception('Invalid UUID provided');
        }

        $repo->delete($uuid, $user);

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function upsertNote(string $uuid, MarkdownNoteRepository $repo, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        if (Uuid::isValid($uuid) === false) {
            throw new Exception('Invalid UUID provided');
        }

        $hydrator = new NoteHydrator(new Library());
        $noteJsonString = $request->getContent();
        $note = $hydrator->getHydratedNote($noteJsonString);
        if ($note === null) {
            throw new Exception("Could not hydrate note with given JSON:\n{$noteJsonString}");
        }

        $noteEntity = $repo->upsert($uuid, $note, $user);

        return $this->json($noteEntity, 200, [], [
            'groups' => ['main'],
        ]);
    }
}
