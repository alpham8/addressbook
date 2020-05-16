<template>
    <div class="container abook-app-wrap">
        <div class="row">
            <div class="col">
                <h1>Contact address book app</h1>
            </div>
        </div>
        <div class="row new-contact-btn-row">
            <div class="col">
                <button type="button" class="btn btn-secondary" v-on:click="onToggleNewContactClicked">add new or edit contact...</button>
            </div>
        </div>
        <transition name="slide">
            <div class="row" v-if="newContactToggle">
                <div class="col">
                    <fieldset class="new-contact-fieldset">
                        <legend>Create new contact</legend>
                        <form method="post" enctype="multipart/form-data" v-on:submit.prevent="onNewAddressSubmitted" accept-charset="UTF-8">
                            <div class="form-group">
                                <label for="firstname">first name</label>
                                <input type="text" id="firstname" class="form-control" name="firstname" aria-describedby="firstnameHelp" v-model="contactMdl.firstname" />
                                <small id="firstnameHelp" class="form-text text-muted">
                                    Please provide here the contact's first name
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="lastname">last name</label>
                                <input type="text" id="lastname" class="form-control" name="lastname" aria-describedby="lastnameHelp" v-model="contactMdl.lastname" />
                                <small id="lastnameHelp" class="form-text text-muted">
                                    Please provide here the contact's last name
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="streetNo">street and number</label>
                                <input type="text" id="streetNo" class="form-control" name="streetNo" aria-describedby="streetNoHelp" v-model="contactMdl.streetNo" />
                                <small id="streetNoHelp" class="form-text text-muted">
                                    Please provide here the contact's street and number
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="zip">zip</label>
                                <input type="text" id="zip" class="form-control" name="zip" aria-describedby="zipHelp" v-model="contactMdl.zip" />
                                <small id="zipHelp" class="form-text text-muted">
                                    Please provide here the contact's zip code
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="city">city</label>
                                <input type="text" id="city" class="form-control" name="city" aria-describedby="cityHelp" v-model="contactMdl.city" />
                                <small id="cityHelp" class="form-text text-muted">
                                    Please provide here the contact's city name
                                </small>
                            </div>
                            <div class="form-group" v-if="countries.length > 0">
                                <label for="country">country</label>
                                <select id="country" name="country" class="form-control" aria-describedby="countryHelp" v-model="contactMdl.countryIsoAlpha2">
                                    <option v-for="country in countries" :value="country.isoAlpha2">
                                        {{country.name}}
                                    </option>
                                </select>
                                <small id="countryHelp" class="form-text text-muted">
                                    Please provide the country in which the contact lives
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="phone">phone number</label>
                                <input type="tel" id="phone" class="form-control" name="phone" aria-describedby="phoneHelp" required="required" v-model="contactMdl.phone" />
                                <small id="phoneHelp" class="form-text text-muted">
                                    Please provide here the contact's first name
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" id="birthday" class="form-control" name="birthday" aria-describedby="birthdayHelp" v-model="contactMdl.birthday" />
                                <small id="birthdayHelp" class="form-text text-muted">
                                    Please provide here the contact's first name
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="email">email address</label>
                                <input type="email" id="email" class="form-control" name="email" aria-describedby="emailHelp" v-model="contactMdl.email" />
                                <small id="emailHelp" class="form-text text-muted">
                                    Please provide here the contact's email address
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="pictureUrl">picture</label>
                                <input type="file" id="pictureUrl" class="form-control" name="pictureUrl" aria-describedby="pictureUrlHelp" />
                                <small id="pictureUrlHelp" class="form-text text-muted">
                                    Please provide here the contact's picture, if any
                                </small>
                            </div>
                            <input type="hidden" name="token" v-bind:value="token" />
                            <input type="hidden" name="contactId" v-bind:value="contactMdl.id" />
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </transition>
        <div class="row contacts-row">
            <div class="col" v-if="contacts.length > 0">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">firstname</th>
                            <th scope="col">lastname</th>
                            <th scope="col">street and number</th>
                            <th scope="col">zip</th>
                            <th scope="col">city</th>
                            <th scope="col">country</th>
                            <th scope="col">phone</th>
                            <th scope="col">birthday</th>
                            <th scope="col">email</th>
                            <th scope="col">picture</th>
                            <th scope="col">actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contact in contacts">
                            <td>{{contact.id}}</td>
                            <td>{{contact.firstname}}</td>
                            <td>{{contact.lastname}}</td>
                            <td>{{contact.streetNo}}</td>
                            <td>{{contact.zip}}</td>
                            <td>{{contact.city}}</td>
                            <td>{{contact.country}}</td>
                            <td><a :href="'tel:' + contact.phone" rel="noopener">{{contact.phone}}</a></td>
                            <td>{{contact.birthday}}</td>
                            <td><a :href="'mailto:' + contact.email" rel="noopener">{{contact.email}}</a></td>
                            <td>
                                <span v-if="contact.pictureUrl">
                                    <img :src="contact.pictureUrl" :alt="contact.firstname + ' ' + contact.lastname + '\'s picture'" class="contact-pic">
                                </span>
                            </td>
                            <td>
                                <button type="button" v-on:click="onEditContactClicked($event, contact)">edit</button>
                                <button type="button" v-on:click="onDeleteContactClicked($event, contact)">delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation" v-if="pageCount > 0">
                    <ul class="pagination">
                        <li class="page-item">
                            <a v-bind:class="'page-link' + (page <= 1 ? ' disabled' : '')" :href="'/overview/getList?page=' + (page - 1)" aria-label="Previous" v-on:click="onPrevPageLinkClicked" v-bind:disabled="page <= 1">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li v-bind:class="'page-item' + (pIt === page ? ' active' : '')" v-for="pIt in pageCount">
                            <a class="page-link" :href="'/overview/getList?page=' + pIt" v-on:click="onPageLinkClicked($event, pIt)">{{pIt}}</a>
                        </li>
                        <li class="page-item">
                            <a v-bind:class="'page-link' + (page >= pageCount ? ' disabled' : '')" :href="'/overview/getList?page=' + (page + 1)" aria-label="Next" v-on:click="onNextPageLinkClicked" v-bind:disabled="page >= pageCount">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div id="modal-error" v-bind:class="showErrorModal ? 'modal-show' : 'modal-hidden'">
            <div id="modal-error-inner">
                <div class="row">
                    <div class="col-12 col-modal-msg">
                        <div class="alert alert-danger">
                            {{modalErrorMsg}}
                        </div>
                    </div>
                    <div class="col-12 col-modal-btn">
                        <button type="button" class="btn btn-primary" v-on:click="onModalButtonClicked">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-confirm" v-bind:class="showConfirmModal ? 'modal-show' : 'modal-hidden'">
            <div id="modal-confirm-inner">
                <div class="row">
                    <div class="col-12 col-modal-msg">
                        <div class="alert alert-danger">
                            Are you sure that you want to delete {{contactMdl.firstname}} {{contactMdl.lastname}}&quest;
                        </div>
                    </div>
                    <div class="col-12 col-modal-btn">
                        <button type="button" class="btn btn-primary" v-on:click="onModalConfirmButtonCancelClicked">cancel</button>
                        <button type="button" class="btn btn-warning btn-confirm-ok" v-on:click="onModalConfirmButtonOkClicked">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import axios from 'axios';

    @Component({
        components: {
        },
    })
    export default class App extends Vue
    {
        // das ! ist noetig fuer ist einfach so da ohne Konstruktor
        public countries: {isoAlpha2: string, name: string}[] = [];
        public contacts: {
            id: number,
            firstname: string,
            lastname: string,
            streetNo: string,
            zip: string,
            city: string,
            countryIsoAlpha2: string,
            country: string,
            phone: string,
            birthday: string,
            email: string,
            pictureUrl: string|null
        }[] = [];

        public contactMdl: {
            id: number,
            firstname: string,
            lastname: string,
            streetNo: string,
            zip: string,
            city: string,
            countryIsoAlpha2: string,
            phone: string,
            birthday: string,
            email: string,
            pictureUrl: string|null
        } = {
            id: -1,
            firstname: '',
            lastname: '',
            streetNo: '',
            zip: '',
            city: '',
            countryIsoAlpha2: '',
            phone: '',
            birthday: '',
            email: '',
            pictureUrl: null
        };

        public pageCount: number = 0;

        public page: number = 1;

        public token: string|null;

        public newContactToggle: boolean = false;

        public showErrorModal: boolean = false;

        public modalErrorMsg: string = '';

        public showConfirmModal: boolean = false;

        constructor()
        {
            super();
            this.token = (<HTMLInputElement>document.getElementById('get-token')).value;
            this.fillCountryList();
            this.renderContactList();
            this.renderPagination();
        }

        fillCountryList(): void
        {
            let me = this;
            axios.get('/overview/getCountries').then(
                response =>
                {
                    this.countries = response.data;
                }
            );
        }

        onNewAddressSubmitted(eventArgs: any): void
        {
            let frmData = new FormData(eventArgs.target as HTMLFormElement);
            axios.post(
                '/overview/saveContact',
                frmData,
                {
                    'headers': {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then((result) =>
                {
                    this.onSaveContactSuccess(result);
                }
            ).catch((error) =>
                {
                    this.onSaveContactError(error);
                }
            );
        }

        onSaveContactSuccess(result: any): void
        {
            if (!result.data.success) {
                this.onSaveContactError(result);
            } else {
                this.renderContactList();
                this.renderPagination();
            }
        }

        onSaveContactError(error: any): void
        {
            this.modalErrorMsg = error.data.result;
            this.showErrorModal = true;
        }

        renderContactList(): void
        {
            let me = this;

            // TypeScript workaround for some strange reason:
            if (typeof this.page === 'undefined') {
                this.page = 1;
            }

            axios.get('/overview/getList?page=' + this.page).then(
                response =>
                {
                    this.contacts = response.data.contacts;
                    this.doRenderContactList();
                }
            );
        }

        doRenderContactList(): void
        {

        }

        renderPagination(): void
        {
            let me = this;

            axios.get('/overview/getPageCount').then(
                response =>
                {
                    this.pageCount = response.data.pages;
                    this.doRenderPagination();
                }
            );
        }

        doRenderPagination(): void
        {

        }

        onToggleNewContactClicked(eventArgs: Event): void
        {
            this.newContactToggle = !this.newContactToggle;
        }

        onModalButtonClicked(eventArgs: Event): void
        {
            this.showErrorModal = false;
            this.modalErrorMsg = '';
        }

        onPrevPageLinkClicked(eventArgs: Event): void
        {
            eventArgs.preventDefault();

            if (this.page <= 1) {
                return;
            }

            this.page--;
            this.renderContactList();
            this.renderPagination();
        }

        onNextPageLinkClicked(eventArgs: Event): void
        {
            eventArgs.preventDefault();
            if (this.page >= this.pageCount) {
                return;
            }

            this.page++;
            this.renderContactList();
            this.renderPagination();
        }

        onPageLinkClicked(eventArgs: Event, page: number): void
        {
            eventArgs.preventDefault();
            this.page = page;
            this.renderContactList();
            this.renderPagination();
        }

        onEditContactClicked(
            eventArgs: Event,
            contact: {
                id: number,
                firstname: string,
                lastname: string,
                streetNo: string,
                zip: string,
                city: string,
                countryIsoAlpha2: string,
                phone: string,
                birthday: string,
                email: string,
                pictureUrl: string|null
        }): void
        {
            this.contactMdl = contact;
            this.newContactToggle = true;
        }

        onDeleteContactClicked(
            eventArgs: Event,
            contact: {
                id: number,
                firstname: string,
                lastname: string,
                streetNo: string,
                zip: string,
                city: string,
                countryIsoAlpha2: string,
                phone: string,
                birthday: string,
                email: string,
                pictureUrl: string|null
            }): void
        {
            this.contactMdl = contact;
            this.showConfirmModal = true;
        }

        onModalConfirmButtonCancelClicked(eventArgs: Event): void
        {
            this.resetContactMdl();
            this.showConfirmModal = false;
        }

        onModalConfirmButtonOkClicked(eventArgs: Event): void
        {
            axios.post(
                '/overview/deleteContact',
                {'id': this.contactMdl.id},
            ).then((result) =>
                {
                    this.onAfterDeleteContactSuccess(result);
                }
            ).catch((error) =>
                {
                    this.onAfterDeleteContactError(error);
                }
            );
        }

        onAfterDeleteContactSuccess(result: any): void
        {
            if (result.data.success) {
                this.resetContactMdl();
                this.showConfirmModal = false;
                this.renderContactList();
                this.renderPagination();
            } else {
                this.onAfterDeleteContactError(result)
            }
        }

        onAfterDeleteContactError(result: any): void
        {
            this.resetContactMdl();
            this.showConfirmModal = false;
            this.modalErrorMsg = result.data.result;
            this.showErrorModal = true;
        }

        resetContactMdl(): void
        {
            this.contactMdl = {
                id: -1,
                firstname: '',
                lastname: '',
                streetNo: '',
                zip: '',
                city: '',
                countryIsoAlpha2: '',
                phone: '',
                birthday: '',
                email: '',
                pictureUrl: null
            };
        }
    }
</script>
