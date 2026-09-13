const assert = require('node:assert/strict');
const M = require('../assets/notation/model.js');
const note = (duration, pitch = 'C4', dots = 0, rest = false) => ({duration, pitch, dots, rest, accidental: ''});
const sheet = () => M.fresh();
{
  const s = sheet(); M.insert(s, 0, note('h')); M.insert(s, 0, note('q'));
  assert.equal(M.insert(s, 0, note('h')), 1);
  assert.deepEqual(s.score.measures.slice(0, 2).map(b => b.notes.map(M.ticks)), [[32,16,16],[16]]);
  assert.equal(s.score.measures[0].notes[2].tieNext, true);
  assert.equal(s.score.measures[1].notes[0].tiePrevious, true);
  const events = M.timeline(s).events;
  assert.equal(events[3].hz, 0, 'a tied continuation must not reattack');
  assert.equal(events[2].soundLength, 1.2);
}
{
  const s = sheet(); M.insert(s, 0, note('h', 'D4', 1)); M.insert(s, 1, note('q', 'E4'));
  M.insert(s, 0, note('h'));
  assert.deepEqual(s.score.measures[1].notes.map(n => n.pitch), ['C4','E4'], 'overflow comes before existing notes');
}
{
  const s = sheet(); M.insert(s, 0, note('h')); M.insert(s, 0, note('q')); M.insert(s, 0, note('h','B4',0,true));
  assert.ok(s.score.measures.flatMap(b=>b.notes).every(n=>!n.tieNext&&!n.tiePrevious));
}
{
  const s = sheet(); s.score.measures = Array.from({length:64}, () => ({notes:[note('w')]}));
  const before = JSON.stringify(s); assert.throws(()=>M.insert(s,63,note('q')), /64 measures/);
  assert.equal(JSON.stringify(s),before,'failed overflow is atomic');
}
{
  const s = sheet(); M.insert(s,0,note('q'));M.insert(s,0,note('q','D4')); M.insert(s,0,note('w'),0);
  assert.equal(s.score.measures[0].notes[0].duration,'w');
  assert.equal(s.score.measures[1].notes[0].pitch,'D4');
}
console.log('Notation model: split, tie playback, occupied bars, rests, limits and replacement passed.');
{
  const s=sheet(); M.insert(s,0,note('h', 'C4',1));M.insert(s,0,note('8','C4',2));M.insert(s,0,note('64','C4',2));
  M.insert(s,0,note('q'));
  assert.equal(s.score.measures[0].notes.reduce((sum,n)=>sum+M.ticks(n),0),64);
  assert.equal(s.score.measures[1].notes.reduce((sum,n)=>sum+M.ticks(n),0),15.75);
}
