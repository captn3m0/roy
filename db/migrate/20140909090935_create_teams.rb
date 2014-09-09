class CreateTeams < ActiveRecord::Migration
  def change
    create_table :teams do |t|
      t.string :identifier
      t.string :name
    end
    add_index :teams, :identifier
    add_index :teams, :name
  end
end
