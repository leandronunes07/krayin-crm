<?php

namespace Webkul\Email\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;

class AttachmentRepository extends Repository
{
    /**
     * Parser object
     *
     * @var \Webkul\Email\Helpers\Parser
     */
    protected $emailParser;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Email\Contracts\Attachment';
    }

    /**
     * @param  \Webkul\Email\Helpers\Parser  $emailParser
     * @return self
     */
    public function setEmailParser($emailParser)
    {
        $this->emailParser = $emailParser;

        return $this;
    }

    /**
     * Upload de anexos do e-mail, separando por projeto
     *
     * @param  \Webkul\Email\Contracts\Email  $email
     * @param  array  $data
     * @return void
     */
    public function uploadAttachments($email, array $data)
    {
        // Obtém o ID do projeto e preenche com zeros à esquerda (7 dígitos)
        $projectId = str_pad(optional($GLOBALS['dbMonitor'])->id ?? 0, 7, '0', STR_PAD_LEFT);

        if (!isset($data['source'])) {
            return;
        }

        if ($data['source'] == 'email') {
            foreach ($this->emailParser->getAttachments() as $attachment) {
                // Define o caminho com o ID do projeto
                $path = "emails/{$projectId}/{$email->id}/{$attachment->getFilename()}";

                // Armazena o arquivo
                Storage::put($path, $attachment->getContent());

                // Salva os metadados no banco
                $this->create([
                    'path'         => $path,
                    'name'         => $attachment->getFileName(),
                    'content_type' => $attachment->contentType,
                    'content_id'   => $attachment->contentId,
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        } else {
            if (!isset($data['attachments'])) {
                return;
            }

            foreach ($data['attachments'] as $index => $attachment) {
                // Define o caminho com o ID do projeto
                $path = request()->file("attachments.{$index}")->store("emails/{$projectId}/{$email->id}");

                // Salva os metadados no banco
                $this->create([
                    'path'         => $path,
                    'name'         => $attachment->getClientOriginalName(),
                    'content_type' => $attachment->getClientMimeType(),
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        }
    }
}
