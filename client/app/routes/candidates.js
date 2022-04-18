import Route from '@ember/routing/route';
import { inject as service } from '@ember/service';
import { action } from '@ember/object';

export default class CandidatesRoute extends Route {
  @service store

  model ()
  {
    return this.store.query('applicant', {})
  }

  @action
  refreshModel () {
    this.refresh();
  }
}
