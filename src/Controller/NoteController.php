<?php

namespace App\Controller;

use App\Entity\MarkdownNote;
use App\Entity\NoteTag;
use App\Entity\User;
use App\Repository\MarkdownNoteRepository;
use DateTime;
use Exception;
use shmolf\NotedHydrator\NoteHydrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class NoteController extends AbstractController
{
    public function getNoteList(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $notes = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findBy(['userId' => $user->getId()], ['lastModified' => 'DESC']);

        $noteList = array_map(function(MarkdownNote $note) {
            return [
                'title' => $note->getTitle(),
                'tags' => array_map(function(NoteTag $tag) {
                        return $tag->getName();
                    }, $note->getTags()->toArray()),
                'clientUuid' => $note->getClientUuid(),
                'inTrashcan' => $note->getInTrashcan(),
                'createdDate' => $note->getCreatedDate(),
                'lastModified' => $note->getLastModified(),
            ];
        }, $notes);

        return new JsonResponse($noteList);
    }

    public function getNoteByClientUuid(string $uuid): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $note = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findOneBy(['userId' => $user->getId(), 'clientUuid' => $uuid]);

        return $this->json($note, 200, [], [
            'groups' => ['main'],
        ]);
    }

    public function getNotesForUser(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $notes = $this->getDoctrine()
            ->getRepository(MarkdownNote::class)
            ->findBy(['userId' => $user->getId()]);

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

    public function deleteNoteByClientUuid(string $uuid, MarkdownNoteRepository $repo): JsonResponse
    {
        $didDelete = $repo->delete($uuid, $this->getUser());

        return new JsonResponse(null, ($didDelete ? 200 : 404));
    }

    public function upsertNote(MarkdownNoteRepository $repo, Request $request): JsonResponse
    {
        $hydrator = new NoteHydrator();
        $noteJsonString = $request->getContent();
        $note = $hydrator->getHydratedNote($noteJsonString);
        if ($note === null) {
            throw new Exception("Could not hydrate note with given JSON:\n{$noteJsonString}");
        }

        $noteEntity = $repo->upsert($note, $this->getUser());

        return $this->json($noteEntity, 200, [], [
            'groups' => ['main'],
        ]);
    }
}
