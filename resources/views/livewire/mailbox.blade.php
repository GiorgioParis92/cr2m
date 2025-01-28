
<div>
    <div class="mb-3">
        <button wire:click="testConnection" class="btn btn-info">Tester la connexion</button>
    </div>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-4">
            <button wire:click="loadInbox" class="btn btn-primary mb-3">Boîte de réception</button>
            <button wire:click="loadSent" class="btn btn-secondary mb-3">Messages envoyés</button>
            <button wire:click="composeEmail" class="btn btn-success mb-3">Nouveau message</button>

            <h5>Paramètres de messagerie</h5>
            <form wire:submit.prevent="saveMailSettings">
                <div class="mb-3">
                    <label for="mail_host" class="form-label">Serveur SMTP</label>
                    <input type="text" wire:model="mail_host" class="form-control" id="mail_host" required>
                </div>
                <div class="mb-3">
                    <label for="mail_port" class="form-label">Port</label>
                    <input type="number" wire:model="mail_port" class="form-control" id="mail_port" required>
                </div>
                <div class="mb-3">
                    <label for="mail_username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" wire:model="mail_username" class="form-control" id="mail_username" required>
                </div>
                <div class="mb-3">
                    <label for="mail_password" class="form-label">Mot de passe</label>
                    <input type="password" wire:model="mail_password" class="form-control" id="mail_password" required>
                </div>
                <div class="mb-3">
                    <label for="mail_encryption" class="form-label">Encryption</label>
                    <select wire:model="mail_encryption" class="form-select" id="mail_encryption">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="">Aucun</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
        <div class="col-md-8">
            @if($showCompose)
                <form wire:submit.prevent="sendEmail">
                    <div class="mb-3">
                        <label for="to" class="form-label">À :</label>
                        <input type="email" wire:model="to" class="form-control" id="to" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Objet :</label>
                        <input type="text" wire:model="subject" class="form-control" id="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Message :</label>
                        <textarea wire:model="body" class="form-control" id="body" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            @else
                <h5>Emails</h5>
                <ul class="list-group">
                    @foreach($emails as $email)
                        <li class="list-group-item">
                            <strong>{{ $email->subject }}</strong> - {{ $email->sender }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

