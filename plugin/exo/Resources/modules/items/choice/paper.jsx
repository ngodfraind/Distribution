import React from 'react'
import {PropTypes as T} from 'prop-types'
import classes from 'classnames'

import {SCORE_FIXED} from '../../quiz/enums'
import {Feedback} from '../components/feedback-btn.jsx'
import {SolutionScore} from '../components/score.jsx'
import {WarningIcon} from './utils/warning-icon.jsx'
import {utils} from './utils/utils'
import {PaperTabs} from '../components/paper-tabs.jsx'

export const ChoicePaper = props => {
  return (
    <PaperTabs
      id={props.item.id}
      yours={
        <div className="choice-paper">
          {props.item.solutions.map(solution =>
            <label
              key={utils.answerId(solution.id)}
              htmlFor={utils.answerId(solution.id)}
              className={classes(
                'answer-item choice-item',
                utils.getAnswerClassForSolution(solution, props.answer)
              )}
            >
              {utils.isSolutionChecked(solution, props.answer) ?
                <WarningIcon className="choice-item-tick" solution={solution} answers={props.answer}/> :

                <input
                  id={utils.answerId(solution.id)}
                  className="choice-item-tick"
                  name={utils.answerId(props.item.id)}
                  type={props.item.multiple ? 'checkbox': 'radio'}
                  disabled
                />
              }

              <div
                className="choice-item-content"
                dangerouslySetInnerHTML={{__html: utils.getChoiceById(props.item.choices, solution.id).data}}
              />

              <div className="choice-item-feedback">
                <Feedback
                  id={`${solution.id}-feedback`}
                  feedback={solution.feedback}
                />
              </div>

              {props.showScore && SCORE_FIXED !== props.item.score.type &&
                <SolutionScore score={solution.score} />
              }
            </label>
          )}
        </div>
      }
      expected={
        <div className="choice-paper">
          {props.item.solutions.map(solution =>
            <label
              key={utils.expectedId(solution.id)}
              htmlFor={utils.expectedId(solution.id)}
              className={classes(
                'answer-item choice-item',
                {
                  'selected-answer': solution.score > 0
                }
              )}
            >
              <input
                className="choice-item-tick"
                checked={solution.score > 0}
                id={utils.expectedId(solution.id)}
                name={utils.expectedId(props.item.id)}
                type={props.item.multiple ? 'checkbox': 'radio'}
                disabled
              />

              <div
                className="choice-item-content"
                dangerouslySetInnerHTML={{__html: utils.getChoiceById(props.item.choices, solution.id).data}}
              />

              <div className="choice-item-feedback">
                <Feedback
                  id={`${solution.id}-feedback-expected`}
                  feedback={solution.feedback}
                />
              </div>

              {props.showScore && SCORE_FIXED !== props.item.score.type &&
                <SolutionScore score={solution.score} />
              }
            </label>
          )}
        </div>
      }
    />
  )
}

ChoicePaper.propTypes = {
  item: T.shape({
    id: T.string.isRequired,
    score: T.shape({
      type: T.string.isRequired
    }),
    choices: T.arrayOf(T.shape({
      id: T.string.isRequired,
      data: T.string.isRequired
    })).isRequired,
    multiple: T.bool.isRequired,
    solutions: T.arrayOf(T.object)
  }).isRequired,
  answer: T.array,
  showScore: T.bool.isRequired
}

ChoicePaper.defaultProps = {
  answer: []
}
