class CreateItems < ActiveRecord::Migration
  def change
    create_table :items do |t|
      # Fields
      t.text :text
      t.integer :timestamp
      t.integer :team_id
      t.integer :channel_id
      t.integer :user_id
      t.timestamps

      # Associations
      t.belongs_to :team
      t.belongs_to :channel
      t.belongs_to :user
    end
    
    # Add foreign keys
    add_foreign_key :items, :teams
    add_foreign_key :items, :channels
    add_foreign_key :items, :users

    # Add indexes
    add_index :items, :team_id
    add_index :items, :channel_id
    add_index :items, :user_id
    add_index :items, :timestamp
  end
end
