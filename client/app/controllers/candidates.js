import Controller from '@ember/controller';
import { action } from '@ember/object';

export default class CandidatesController extends Controller {

  @action
  addNew () {
    if( candidateName.value && candidateAge.value ) {
      if(isNaN(candidateAge.value)) {
        alert('Age must be a number');
      } else {
        let candidate = this.store.createRecord('applicant', {
          name: candidateName.value,
          age: candidateAge.value
        });
        candidate.save().then(() => {
          alert('Saved. Name: ' + candidateName.value + ' Age: ' + candidateAge.value);
          candidateName.value = '';
          candidateAge.value = '';
          this.send('refreshModel');
        }, (errors) => {
          if(errors.errors) {
            alert(errors.errors[0]);
          }
        });
      }
    } else {
      alert('Name & age are both required');
    }
  }

}
